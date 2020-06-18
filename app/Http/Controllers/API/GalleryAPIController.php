<?php

namespace App\Http\Controllers\API;


use App\Models\Gallery;
use App\Repositories\GalleryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\Response;
use Prettus\Repository\Exceptions\RepositoryException;
use App\Repositories\MarketRepository;
use Flash;

/**
 * Class GalleryController
 * @package App\Http\Controllers\API
 */

class GalleryAPIController extends Controller
{
    /** @var  GalleryRepository */
    private $galleryRepository;
    private $marketRepository;

    public function __construct(GalleryRepository $galleryRepo, MarketRepository $marketRepository)
    {
        $this->galleryRepository = $galleryRepo;
        $this->marketRepository = $marketRepository;
    }

    /**
     * Display a listing of the Gallery.
     * GET|HEAD /galleries
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{
            $this->galleryRepository->pushCriteria(new RequestCriteria($request));
            $this->galleryRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            Flash::error($e->getMessage());
        }
        $galleries = $this->galleryRepository->all();
        foreach($galleries as $gallery)
        {
            $gallery->market_name= $this->marketRepository->findByField('id', $gallery->market_id)->first()->name;
        }

        return $this->sendResponse($galleries->toArray(), 'Galleries retrieved successfully');
    }

    /**
     * Display the specified Gallery.
     * GET|HEAD /galleries/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Gallery $gallery */
        if (!empty($this->galleryRepository)) {
            $gallery = $this->galleryRepository->findWithoutFail($id);
        }

        if (empty($gallery)) {
            return $this->sendError('Gallery not found');
        }

        return $this->sendResponse($gallery->toArray(), 'Gallery retrieved successfully');
    }
}
