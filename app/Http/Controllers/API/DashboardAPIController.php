<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Repositories\FieldRepository;
use App\Repositories\MarketRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\UserRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class FieldController
 * @package App\Http\Controllers\API
 */

class DashboardAPIController extends Controller
{
    /** @var  FieldRepository */
    private $fieldRepository;

    /** @var  OrderRepository */
    private $orderRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /** @var  MarketRepository */
    private $marketRepository;
    /** @var  PaymentRepository */
    private $paymentRepository;

    public function __construct(OrderRepository $orderRepo, UserRepository $userRepo, PaymentRepository $paymentRepo, MarketRepository $marketRepo)
    {
        $this->orderRepository = $orderRepo;
        $this->userRepository = $userRepo;
        $this->marketRepository = $marketRepo;
        $this->paymentRepository = $paymentRepo;
    }
    /**
     * Display a listing of the Field.
     * GET|HEAD /fields
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $ordersCount = $this->orderRepository->count();
        $membersCount = $this->userRepository->count();
        $marketsCount = $this->marketRepository->count();
        $markets = $this->marketRepository->limit(4)->get();
        $earning = $this->paymentRepository->all()->sum('price');
        $data=array("total_orders"=>$ordersCount, "total_clients"=>$membersCount, "total_markets"=>$marketsCount, "typical_markets"=>$markets, "total_earnings"=>$earning);
        return $this->sendResponse($data, 'Dashboard data retrieved successfully');
    }
}
