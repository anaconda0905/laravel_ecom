<?php

namespace App\Http\Controllers\API;


use App\Models\Option;
use App\Repositories\ProductRepository;
use App\Repositories\OptionRepository;
use App\Repositories\OptionGroupRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\Response;
use Prettus\Repository\Exceptions\RepositoryException;
use Flash;
use App\Repositories\MarketRepository;
/**
 * Class OptionController
 * @package App\Http\Controllers\API
 */

class OptionAPIController extends Controller
{
    /** @var  OptionRepository */
    private $optionRepository;
    private $optionGroupRepository;
    private $productRepository;
    private $marketRepository;

    public function __construct(OptionRepository $optionRepo, MarketRepository $marketRepository, OptionGroupRepository $optionGroupRepository, ProductRepository $productRepository)
    {
        $this->optionRepository = $optionRepo;
        $this->optionGroupRepository = $optionGroupRepository;
        $this->productRepository = $productRepository;
        $this->marketRepository = $marketRepository;
    }

    /**
     * Display a listing of the Option.
     * GET|HEAD /options
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{
            $this->optionRepository->pushCriteria(new RequestCriteria($request));
            $this->optionRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            Flash::error($e->getMessage());
        }
        $options = $this->optionRepository->all();
        foreach($options as $option)
        {
            $option->product_name = $this->productRepository->findByField('id', $option->product_id)->first()->name;
            $option->market_name =  $this->marketRepository->findByField('id', $this->productRepository->findByField('id', $option->product_id)->first()->market_id)->first()->name;
            $option->option_group_name = $this->optionGroupRepository->findByField('id', $option->option_group_id)->first()->name;
        }
        return $this->sendResponse($options->toArray(), 'Options retrieved successfully');
    }

    /**
     * Display the specified Option.
     * GET|HEAD /options/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Option $option */
        if (!empty($this->optionRepository)) {
            $option = $this->optionRepository->findWithoutFail($id);
        }

        if (empty($option)) {
            return $this->sendError('Option not found');
        }

        return $this->sendResponse($option->toArray(), 'Option retrieved successfully');
    }
}
