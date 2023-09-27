<?php

namespace SOSTheBlack\Repository\Presenter;

use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\SerializerAbstract;
use League\Fractal\TransformerAbstract;
use SOSTheBlack\Repository\Contracts\PresenterInterface;

/**
 * Class FractalPresenter
 * @package SOSTheBlack\Repository\Presenter
 */
abstract class FractalPresenter implements PresenterInterface
{
    /**
     * @var string|null
     */
    protected ?string $resourceKeyItem = null;

    /**
     * @var string|null
     */
    protected ?string $resourceKeyCollection = null;

    /**
     * @var Manager|null
     */
    protected ?Manager $fractal = null;

    /**
     * @var Collection|null
     */
    protected ?Collection $resource = null;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if (!class_exists('League\Fractal\Manager')) {
            throw new Exception(trans('repository::packages.league_fractal_required'));
        }

        $this->fractal = new Manager();
        $this->parseIncludes();
        $this->setupSerializer();
    }

    /**
     * @return $this
     */
    protected function parseIncludes(): static
    {

        $request = app('Illuminate\Http\Request');
        $paramIncludes = config('repository.fractal.params.include', 'include');

        if ($request->has($paramIncludes)) {
            $this->fractal->parseIncludes($request->get($paramIncludes));
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function setupSerializer(): static
    {
        $serializer = $this->serializer();

        if ($serializer instanceof SerializerAbstract) {
            $this->fractal->setSerializer(new $serializer());
        }

        return $this;
    }

    /**
     * Get Serializer
     *
     * @return SerializerAbstract
     */
    public function serializer(): SerializerAbstract
    {
        $serializer = config('repository.fractal.serializer', 'League\\Fractal\\Serializer\\DataArraySerializer');

        return new $serializer();
    }

    /**
     * Prepare data to present
     *
     * @param $data
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function present($data): mixed
    {
        if (!class_exists('League\Fractal\Manager')) {
            throw new Exception(trans('repository::packages.league_fractal_required'));
        }

        if ($data instanceof EloquentCollection) {
            $this->resource = $this->transformCollection($data);
        } elseif ($data instanceof AbstractPaginator) {
            $this->resource = $this->transformPaginator($data);
        } else {
            $this->resource = $this->transformItem($data);
        }

        return $this->fractal->createData($this->resource)->toArray();
    }

    /**
     * @param $data
     *
     * @return Collection
     */
    protected function transformCollection($data): Collection
    {
        return new Collection($data, $this->getTransformer(), $this->resourceKeyCollection);
    }

    /**
     * Transformer
     *
     * @return TransformerAbstract
     */
    abstract public function getTransformer(): TransformerAbstract;

    /**
     * @param AbstractPaginator|LengthAwarePaginator|Paginator $paginator
     *
     * @return Collection
     */
    protected function transformPaginator(Paginator|LengthAwarePaginator|AbstractPaginator $paginator): Collection
    {
        $collection = $paginator->getCollection();
        $resource = new Collection($collection, $this->getTransformer(), $this->resourceKeyCollection);
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));

        return $resource;
    }

    /**
     * @param $data
     *
     * @return Item
     */
    protected function transformItem($data): Item
    {
        return new Item($data, $this->getTransformer(), $this->resourceKeyItem);
    }
}
