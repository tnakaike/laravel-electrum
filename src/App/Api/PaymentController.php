<?php

namespace AraneaDev\Electrum\App\Api;

use Illuminate\Http\Request;
use AraneaDev\Electrum\Electrum;
use App\Http\Controllers\Controller;

/**
 * Class PaymentController.
 */
class PaymentController extends Controller
{
    /** @var Electrum */
    protected $electrum;

    /**
     * PaymentController constructor.
     *
     * @param Electrum $electrum
     */
    public function __construct(Electrum $electrum)
    {
        $this->middleware(config('electrum.web_interface.middleware', ['web', 'auth']));
        $this->electrum = $electrum;
    }

    /**
     * Create a new payment.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        return response()->json($this->electrum->createPayment(
            $request->get('destination'),
            $request->get('amount'),
	    $request->get('password')
        ));
    }

    /**
     * Sign a transaction.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sign(Request $request)
    {
        return response()->json($this->electrum->sign(
            $request->get('tx'),
	    $request->get('password')
        ));
    }

    /**
     * Get the maximum amount that can be sent
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getmax(Request $request)
    {
        return response()->json($this->electrum->getmax(
            $request->get('destination'),
	    $request->get('password')
        ));
    }

    /**
     * Broadcast a transaction.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function broadcast(Request $request)
    {
        return response()->json($this->electrum->broadcast(
            $request->get('tx')
        ));
    }
}
