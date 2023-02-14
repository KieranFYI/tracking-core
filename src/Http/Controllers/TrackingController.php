<?php

namespace KieranFYI\Tracking\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use KieranFYI\Tracking\Core\Interfaces\TrackingInterface;
use KieranFYI\Tracking\Core\Models\TrackingItem;
use KieranFYI\Tracking\Core\Models\TrackingRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class TrackingController extends Controller
{
    /**
     * @param Request $request
     * @param mixed $id
     * @param TrackingItem $item
     * @return Response
     * @throws Throwable
     */
    public function show(Request $request, mixed $key, TrackingItem $item): Response
    {
        $log = new TrackingRequest([
            'ip' => $request->ip(),
            'ips' => $request->ips(),
            'trusted_proxy' => $request->isFromTrustedProxy(),
            'user_agent' => $request->userAgent(),
            'referer' => request()->headers->get('referer'),
        ]);
        $log->tracking()->associate($item->tracking_id);
        $log->item()->associate($item);
        $log->user()->associate(Auth::user());
        $log->save();

        try {
            abort_if($key != $item->getKey(), 400);
            $item->load('user');
            if (!is_null($item->user)) {
                // TODO show login page
                abort_if(!$item->user->is(Auth::user()), 401);
            }

            $item->load('trackable');
            abort_if(!($item->trackable instanceof TrackingInterface), 405);
            $response = $item->trackable->buildResponse($item);

            $log->update([
                'status_code' => $response->getStatusCode()
            ]);

            return $response;
        } catch (HttpExceptionInterface $e) {
            $log->update([
                'status_code' => $e->getStatusCode()
            ]);
            throw $e;
        } catch (Throwable $t) {
            $log->update([
                'status_code' => 500
            ]);
            throw $t;
        }
    }
}