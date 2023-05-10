<?php

namespace SOSTheBlack\Repository\Eloquent;

use Closure;
use Exception;
use Illuminate\Container\Container as Application;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use SOSTheBlack\Repository\Contracts\CriteriaInterface;
use SOSTheBlack\Repository\Contracts\Presentable;
use SOSTheBlack\Repository\Contracts\PresenterInterface;
use SOSTheBlack\Repository\Contracts\RepositoryCriteriaInterface;
use SOSTheBlack\Repository\Contracts\RepositoryInterface;
use SOSTheBlack\Repository\Events\RepositoryEntityCreated;
use SOSTheBlack\Repository\Events\RepositoryEntityCreating;
use SOSTheBlack\Repository\Events\RepositoryEntityDeleted;
use SOSTheBlack\Repository\Events\RepositoryEntityDeleting;
use SOSTheBlack\Repository\Events\RepositoryEntityUpdated;
use SOSTheBlack\Repository\Events\RepositoryEntityUpdating;
use SOSTheBlack\Repository\Exceptions\RepositoryException;
use SOSTheBlack\Repository\Traits\ComparesVersionsTrait;

/**
 * Class BaseRepository
 *

 * @package SOSTheBlack\Repository\Eloquent
 * @author  Anderson Andrade <contato@andersonandra.de>
 */
abstract class BaseRepository implements RepositoryInterface, RepositoryCriteriaInterface
{
    use ComparesVersionsTrait;

    /**
     * @var Application
     */
    protected Application $app;

    /**
     * @var Model
     */
    protected Model $model;

    /**
     * @var array
     */
    protected array $fieldSearchable = [];

    /**
     * @var PresenterInterface
     */
    protected PresenterInterface $presenter;

    /**
     * @var ValidatorInterface
     */
    protected ValidatorInterface $validator;

    /**
     * Validation Rules
     *
     * @var ?array
     */
    protected ?array $rules = null;

    /**
     * Collection of Criteria
     *
     * @var Collection
     */
    protected Collection $criteria;

    /**
     * @var bool
     */
    protected bool $skipCriteria = false;

    /**
     * @var bool
     */
    protected bool $skipPresenter = false;

    /**
     * @var Closure|null
     */
    protected ?Closure $scopeQuery = null;

    /**
     * @param Application $app
     *
     * @throws RepositoryException
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->criteria = new Collection();
        $this->makeModel();
        $this->makePresenter();
        $this->makeValidator();
        $this->boot();
    }

    /**
     * @return Model
     * @throws RepositoryException
     * @throws BindingResolutionException
     */
    public function makeModel(): Model
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new RepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    abstract public function model(): string;

    /**
     * @param null $presenter
     *
     * @return PresenterInterface|null
     *
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function makePresenter($presenter = null): ?PresenterInterface
    {
        $presenter = !is_null($presenter) ? $presenter : $this->presenter();

        if (!is_null($presenter)) {
            $this->presenter = is_string($presenter) ? $this->app->make($presenter) : $presenter;

            if (!$this->presenter instanceof PresenterInterface) {
                throw new RepositoryException("Class {$presenter} must be an instance of SOSTheBlack\\Repository\\Contracts\\PresenterInterface");
            }

            return $this->presenter;
        }

        return null;
    }

    /**
     * Specify Presenter class name
     *
     * @return string|null
     */
    public function presenter(): ?string
    {
        return null;
    }

    /**
     * @param null $validator
     *
     * @return null|ValidatorInterface
     * @throws RepositoryException
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function makeValidator($validator = null): ?ValidatorInterface
    {
        $validator = !is_null($validator) ? $validator : $this->validator();

        if (!is_null($validator)) {
            $this->validator = is_string($validator) ? $this->app->make($validator) : $validator;

            if (!$this->validator instanceof ValidatorInterface) {
                throw new RepositoryException("Class {$validator} must be an instance of Prettus\\Validator\\Contracts\\ValidatorInterface");
            }

            return $this->validator;
        }

        return null;
    }

    /**
     * Specify Validator class name of Prettus\Validator\Contracts\ValidatorInterface
     *
     * @return ValidatorInterface|null
     * @throws Exception
     */
    public function validator(): ?ValidatorInterface
    {
        if (isset($this->rules) && !is_null($this->rules) && is_array($this->rules) && !empty($this->rules)) {
            if (class_exists('Prettus\Validator\LaravelValidator')) {
                $validator = app('Prettus\Validator\LaravelValidator');
                if ($validator instanceof ValidatorInterface) {
                    $validator->setRules($this->rules);

                    return $validator;
                }
            } else {
                throw new Exception(trans('repository::packages.sostheblack_laravel_validation_required'));
            }
        }

        return null;
    }

    /**
     *
     */
    public function boot(): void
    {
        //
    }

    /**
     * Trigger static method calls to the model
     *
     * @param $method
     * @param $arguments
     *
     * @return mixed
     */
    public static function __callStatic($method, $arguments): mixed
    {
        return call_user_func_array([new static(), $method], $arguments);
    }

    /**
     * Get Searchable Fields
     *
     * @return array
     */
    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    /**
     * Query Scope
     *
     * @param Closure $scope
     *
     * @return $this
     */
    public function scopeQuery(Closure $scope): static
    {
        $this->scopeQuery = $scope;

        return $this;
    }

    /**
     * Retrieve data array for populate field select
     *
     * @param string $column
     * @param string|null $key
     *
     * @return Collection|array
     */
    public function lists(string $column, string $key = null): array|Collection
    {
        $this->applyCriteria();

        return $this->model->lists($column, $key);
    }

    /**
     * Apply criteria in current Query
     *
     * @return $this
     */
    protected function applyCriteria(): static
    {
        if ($this->skipCriteria === true) {
            return $this;
        }

        $criteria = $this->getCriteria();

        if ($criteria) {
            foreach ($criteria as $c) {
                if ($c instanceof CriteriaInterface) {
                    $this->model = $c->apply($this->model, $this);
                }
            }
        }

        return $this;
    }

    /**
     * Get Collection of Criteria
     *
     * @return Collection
     */
    public function getCriteria(): Collection
    {
        return $this->criteria;
    }

    /**
     * Retrieve data array for populate field select
     * Compatible with Laravel 5.3
     *
     * @param string $column
     * @param string|null $key
     *
     * @return Collection|array
     */
    public function pluck(string $column, string $key = null): array|Collection
    {
        $this->applyCriteria();

        return $this->model->pluck($column, $key);
    }

    /**
     * SyncWithoutDetaching
     *
     * @param $id
     * @param $relation
     * @param $attributes
     *
     * @return mixed
     */
    public function syncWithoutDetaching($id, $relation, $attributes): mixed
    {
        return $this->sync($id, $relation, $attributes, false);
    }

    /**
     * Sync relations
     *
     * @param      $id
     * @param      $relation
     * @param      $attributes
     * @param bool $detaching
     *
     * @return mixed
     */
    public function sync($id, $relation, $attributes, bool $detaching = true): mixed
    {
        return $this->find($id)->{$relation}()->sync($attributes, $detaching);
    }

    /**
     * Find data by id
     *
     * @param       $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, array $columns = ['*']): mixed
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->findOrFail($id, $columns);
        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * Apply scope in current Query
     *
     * @return $this
     */
    protected function applyScope(): static
    {
        if (isset($this->scopeQuery) && is_callable($this->scopeQuery)) {
            $callback = $this->scopeQuery;
            $this->model = $callback($this->model);
        }

        return $this;
    }

    /**
     * @throws RepositoryException
     * @throws BindingResolutionException
     */
    public function resetModel(): void
    {
        $this->makeModel();
    }

    /**
     * Wrapper result data
     *
     * @param mixed $result
     *
     * @return mixed
     */
    public function parserResult(mixed $result): mixed
    {
        if ($this->presenter instanceof PresenterInterface) {
            if ($result instanceof Collection || $result instanceof LengthAwarePaginator) {
                $result->each(function ($model) {
                    if ($model instanceof Presentable) {
                        $model->setPresenter($this->presenter);
                    }

                    return $model;
                });
            } else if ($result instanceof Presentable) {
                $result = $result->setPresenter($this->presenter);
            }

            if (!$this->skipPresenter) {
                return $this->presenter->present($result);
            }
        }

        return $result;
    }

    /**
     * Set Presenter
     *
     * @param $presenter
     *
     * @return $this
     */
    public function setPresenter($presenter): mixed
    {
        $this->makePresenter($presenter);

        return $this;
    }

    /**
     * Count results of repository
     *
     * @param array $where
     * @param string $columns
     *
     * @return int
     *
     * @throws RepositoryException
     * @throws BindingResolutionException
     */
    public function count(array $where = [], string $columns = '*'): int
    {
        $this->applyCriteria();
        $this->applyScope();

        if ($where) {
            $this->applyConditions($where);
        }

        $result = $this->model->count($columns);

        $this->resetModel();
        $this->resetScope();

        return $result;
    }

    /**
     * Applies the given where conditions to the model.
     *
     * @param array $where
     *
     * @return void
     * @throws RepositoryException
     */
    protected function applyConditions(array $where): void
    {
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;
                //smooth input
                $condition = preg_replace('/\s\s+/', ' ', trim($condition));

                //split to get operator, syntax: "DATE >", "DATE =", "DAY <"
                $operator = explode(' ', $condition);
                if (count($operator) > 1) {
                    $condition = $operator[0];
                    $operator = $operator[1];
                } else $operator = null;
                switch (strtoupper($condition)) {
                    case 'IN':
                        if (!is_array($val)) throw new RepositoryException("Input {$val} mus be an array");
                        $this->model = $this->model->whereIn($field, $val);
                        break;
                    case 'NOTIN':
                        if (!is_array($val)) throw new RepositoryException("Input {$val} mus be an array");
                        $this->model = $this->model->whereNotIn($field, $val);
                        break;
                    case 'DATE':
                        if (!$operator) $operator = '=';
                        $this->model = $this->model->whereDate($field, $operator, $val);
                        break;
                    case 'DAY':
                        if (!$operator) $operator = '=';
                        $this->model = $this->model->whereDay($field, $operator, $val);
                        break;
                    case 'MONTH':
                        if (!$operator) $operator = '=';
                        $this->model = $this->model->whereMonth($field, $operator, $val);
                        break;
                    case 'YEAR':
                        if (!$operator) $operator = '=';
                        $this->model = $this->model->whereYear($field, $operator, $val);
                        break;
                    case 'EXISTS':
                        if (!($val instanceof Closure)) throw new RepositoryException("Input {$val} must be closure function");
                        $this->model = $this->model->whereExists($val);
                        break;
                    case 'HAS':
                        if (!($val instanceof Closure)) throw new RepositoryException("Input {$val} must be closure function");
                        $this->model = $this->model->whereHas($field, $val);
                        break;
                    case 'HASMORPH':
                        if (!($val instanceof Closure)) throw new RepositoryException("Input {$val} must be closure function");
                        $this->model = $this->model->whereHasMorph($field, $val);
                        break;
                    case 'DOESNTHAVE':
                        if (!($val instanceof Closure)) throw new RepositoryException("Input {$val} must be closure function");
                        $this->model = $this->model->whereDoesntHave($field, $val);
                        break;
                    case 'DOESNTHAVEMORPH':
                        if (!($val instanceof Closure)) throw new RepositoryException("Input {$val} must be closure function");
                        $this->model = $this->model->whereDoesntHaveMorph($field, $val);
                        break;
                    case 'BETWEEN':
                        if (!is_array($val)) throw new RepositoryException("Input {$val} mus be an array");
                        $this->model = $this->model->whereBetween($field, $val);
                        break;
                    case 'BETWEENCOLUMNS':
                        if (!is_array($val)) throw new RepositoryException("Input {$val} mus be an array");
                        $this->model = $this->model->whereBetweenColumns($field, $val);
                        break;
                    case 'NOTBETWEEN':
                        if (!is_array($val)) throw new RepositoryException("Input {$val} mus be an array");
                        $this->model = $this->model->whereNotBetween($field, $val);
                        break;
                    case 'NOTBETWEENCOLUMNS':
                        if (!is_array($val)) throw new RepositoryException("Input {$val} mus be an array");
                        $this->model = $this->model->whereNotBetweenColumns($field, $val);
                        break;
                    case 'RAW':
                        $this->model = $this->model->whereRaw($val);
                        break;
                    default:
                        $this->model = $this->model->where($field, $condition, $val);
                }
            } else {
                $this->model = $this->model->where($field, '=', $value);
            }
        }
    }

    /**
     * Load relation with closure
     *
     * @param string $relation
     * @param Closure $closure
     *
     * @return $this
     */
    public function whereHas(string $relation, Closure $closure): static
    {
        $this->model = $this->model->whereHas($relation, $closure);

        return $this;
    }

    /**
     * Reset Query Scope
     *
     * @return $this
     */
    public function resetScope(): static
    {
        $this->scopeQuery = null;

        return $this;
    }

    /**
     * Retrieve first data of repository
     *
     * @param array $columns
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function first(array $columns = ['*']): mixed
    {
        $this->applyCriteria();
        $this->applyScope();

        $results = $this->model->first($columns);

        $this->resetModel();

        return $this->parserResult($results);
    }

    /**
     * Retrieve first data of repository, or return new Entity
     *
     * @param array $attributes
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function firstOrNew(array $attributes = []): mixed
    {
        $this->applyCriteria();
        $this->applyScope();

        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $model = $this->model->firstOrNew($attributes);
        $this->skipPresenter($temporarySkipPresenter);

        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * Skip Presenter Wrapper
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipPresenter(bool $status = true): static
    {
        $this->skipPresenter = $status;

        return $this;
    }

    /**
     * Retrieve first data of repository, or create new Entity
     *
     * @param array $attributes
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function firstOrCreate(array $attributes = []): mixed
    {
        $this->applyCriteria();
        $this->applyScope();

        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $model = $this->model->firstOrCreate($attributes);
        $this->skipPresenter($temporarySkipPresenter);

        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * Retrieve data of repository with limit applied
     *
     * @param int $limit
     * @param array $columns
     *
     * @return mixed
     */
    public function limit(int $limit, array $columns = ['*']): mixed
    {
        // Shortcut to all with `limit` applied on query via `take`
        $this->take($limit);

        return $this->all($columns);
    }

    /**
     * Set the "limit" value of the query.
     *
     * @param int $limit
     *
     * @return $this
     */
    public function take(int $limit): static
    {
        // Internally `take` is an alias to `limit`
        $this->model = $this->model->limit($limit);

        return $this;
    }

    /**
     * Retrieve all data of repository
     *
     * @param array $columns
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function all(array $columns = ['*']): mixed
    {
        $this->applyCriteria();
        $this->applyScope();

        if ($this->model instanceof Builder) {
            $results = $this->model->get($columns);
        } else {
            $results = $this->model->all($columns);
        }

        $this->resetModel();
        $this->resetScope();

        return $this->parserResult($results);
    }

    /**
     * Alias of All method
     *
     * @param array $columns
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function get(array $columns = ['*']): mixed
    {
        return $this->all($columns);
    }

    /**
     * Retrieve all data of repository, simple paginated
     *
     * @param int|null $limit
     * @param array $columns
     *
     * @return mixed
     */
    public function simplePaginate(int $limit = null, array $columns = ['*']): mixed
    {
        return $this->paginate($limit, $columns, "simplePaginate");
    }

    /**
     * Retrieve all data of repository, paginated
     *
     * @param null $limit
     * @param array $columns
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function paginate($limit = null, array $columns = ['*']): mixed
    {
        $this->applyCriteria();
        $this->applyScope();
        $limit = is_null($limit) ? config('repository.pagination.limit', 15) : $limit;
        $results = $this->model->{$method}($limit, $columns);
        $results->appends(app('request')->query());
        $this->resetModel();

        return $this->parserResult($results);
    }

    /**
     * Find data by field and value
     *
     * @param       $field
     * @param       $value
     * @param array $columns
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function findByField($field, $value, array $columns = ['*']): mixed
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->where($field, '=', $value)->get($columns);
        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * Find data by multiple fields
     *
     * @param array $where
     * @param array $columns
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function findWhere(array $where, array $columns = ['*']): mixed
    {
        $this->applyCriteria();
        $this->applyScope();

        $this->applyConditions($where);

        $model = $this->model->get($columns);
        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * Find data by multiple values in one field
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function findWhereIn($field, array $values, array $columns = ['*']): mixed
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->whereIn($field, $values)->get($columns);
        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * Find data by excluding multiple values in one field
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function findWhereNotIn($field, array $values, array $columns = ['*']): mixed
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->whereNotIn($field, $values)->get($columns);
        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * Find data by between values in one field
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function findWhereBetween($field, array $values, array $columns = ['*']): mixed
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->whereBetween($field, $values)->get($columns);
        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * Save a new entity in repository
     *
     * @param array $attributes
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     * @throws RepositoryException
     * @throws ValidatorException
     */
    public function create(array $attributes): mixed
    {
        if (!is_null($this->validator)) {
            // we should pass data that has been casts by the model
            // to make sure data type are same because validator may need to use
            // this data to compare with data that fetch from database.
            if ($this->versionCompare($this->app->version(), "5.2.*", ">")) {
                $attributes = $this->model->newInstance()->forceFill($attributes)->makeVisible($this->model->getHidden())->toArray();
            } else {
                $model = $this->model->newInstance()->forceFill($attributes);
                $model->makeVisible($this->model->getHidden());
                $attributes = $model->toArray();
            }

            $this->validator->with($attributes)->passesOrFail(ValidatorInterface::RULE_CREATE);
        }

        event(new RepositoryEntityCreating($this, $attributes));

        $model = $this->model->newInstance($attributes);
        $model->save();
        $this->resetModel();

        event(new RepositoryEntityCreated($this, $model));

        return $this->parserResult($model);
    }

    /**
     * Load relations
     *
     * @param array|string $relations
     *
     * @return $this
     */
    public function with(array|string $relations): static
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * Update a entity in repository by id
     *
     * @param array $attributes
     * @param       $id
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     * @throws RepositoryException
     * @throws ValidatorException
     */
    public function update(array $attributes, $id): mixed
    {
        $this->applyScope();

        if (!is_null($this->validator)) {
            // we should pass data that has been casts by the model
            // to make sure data type are same because validator may need to use
            // this data to compare with data that fetch from database.
            $model = $this->model->newInstance();
            $model->setRawAttributes([]);
            $model->setAppends([]);
            if ($this->versionCompare($this->app->version(), "5.2.*", ">")) {
                $attributes = $model->forceFill($attributes)->makeVisible($this->model->getHidden())->toArray();
            } else {
                $model->forceFill($attributes);
                $model->makeVisible($this->model->getHidden());
                $attributes = $model->toArray();
            }

            $this->validator->with($attributes)->setId($id)->passesOrFail(ValidatorInterface::RULE_UPDATE);
        }

        $temporarySkipPresenter = $this->skipPresenter;

        $this->skipPresenter(true);

        $model = $this->model->findOrFail($id);

        event(new RepositoryEntityUpdating($this, $model));

        $model->fill($attributes);
        $model->save();

        $this->skipPresenter($temporarySkipPresenter);
        $this->resetModel();

        event(new RepositoryEntityUpdated($this, $model));

        return $this->parserResult($model);
    }

    /**
     * Update or Create an entity in repository
     *
     * @param array $attributes
     * @param array $values
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     * @throws RepositoryException
     * @throws ValidatorException
     */
    public function updateOrCreate(array $attributes, array $values = []): mixed
    {
        $this->applyScope();

        if (!is_null($this->validator)) {
            $this->validator->with(array_merge($attributes, $values))->passesOrFail(ValidatorInterface::RULE_CREATE);
        }

        $temporarySkipPresenter = $this->skipPresenter;

        $this->skipPresenter(true);

        event(new RepositoryEntityCreating($this, $attributes));

        $model = $this->model->updateOrCreate($attributes, $values);

        $this->skipPresenter($temporarySkipPresenter);
        $this->resetModel();

        event(new RepositoryEntityUpdated($this, $model));

        return $this->parserResult($model);
    }

    /**
     * Delete multiple entities by given criteria.
     *
     * @param array $where
     *
     * @return int
     *
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function deleteWhere(array $where): int
    {
        $this->applyScope();

        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $this->applyConditions($where);

        event(new RepositoryEntityDeleting($this, $this->model->getModel()));

        $deleted = $this->model->delete();

        event(new RepositoryEntityDeleted($this, $this->model->getModel()));

        $this->skipPresenter($temporarySkipPresenter);
        $this->resetModel();

        return $deleted;
    }

    /**
     * Returns the current Model instance
     *
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Delete a entity in repository by id
     *
     * @param $id
     *
     * @return int
     *
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function delete($id): int
    {
        $this->applyScope();

        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $model = $this->find($id);
        $originalModel = clone $model;

        $this->skipPresenter($temporarySkipPresenter);
        $this->resetModel();

        event(new RepositoryEntityDeleting($this, $model));

        $deleted = $model->delete();

        event(new RepositoryEntityDeleted($this, $originalModel));

        return $deleted;
    }

    /**
     * Check if entity has relation
     *
     * @param string $relation
     *
     * @return $this
     */
    public function has(string $relation): static
    {
        $this->model = $this->model->has($relation);

        return $this;
    }

    /**
     * Add subselect queries to count the relations.
     *
     * @param mixed $relations
     *
     * @return $this
     */
    public function withCount(mixed $relations): static
    {
        $this->model = $this->model->withCount($relations);
        return $this;
    }

    /**
     * Set hidden fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function hidden(array $fields): static
    {
        $this->model->setHidden($fields);

        return $this;
    }

    /**
     * Set the "orderBy" value of the query.
     *
     * @param string $column
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy(string $column, string $direction = 'asc'): static
    {
        $this->model = $this->model->orderBy($column, $direction);

        return $this;
    }

    /**
     * Set visible fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function visible(array $fields): static
    {
        $this->model->setVisible($fields);

        return $this;
    }

    /**
     * Push Criteria for filter the query
     *
     * @param $criteria
     *
     * @return $this
     * @throws RepositoryException
     */
    public function pushCriteria($criteria): static
    {
        if (is_string($criteria)) {
            $criteria = new $criteria;
        }
        if (!$criteria instanceof CriteriaInterface) {
            throw new RepositoryException("Class " . get_class($criteria) . " must be an instance of SOSTheBlack\\Repository\\Contracts\\CriteriaInterface");
        }
        $this->criteria->push($criteria);

        return $this;
    }

    /**
     * Pop Criteria
     *
     * @param $criteria
     *
     * @return $this
     */
    public function popCriteria($criteria): static
    {
        $this->criteria = $this->criteria->reject(function ($item) use ($criteria) {
            if (is_object($item) && is_string($criteria)) {
                return get_class($item) === $criteria;
            }

            if (is_string($item) && is_object($criteria)) {
                return $item === get_class($criteria);
            }

            return get_class($item) === get_class($criteria);
        });

        return $this;
    }

    /**
     * Find data by Criteria
     *
     * @param CriteriaInterface $criteria
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function getByCriteria(CriteriaInterface $criteria): mixed
    {
        $this->model = $criteria->apply($this->model, $this);
        $results = $this->model->get();
        $this->resetModel();

        return $this->parserResult($results);
    }

    /**
     * Skip Criteria
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipCriteria($status = true): static
    {
        $this->skipCriteria = $status;

        return $this;
    }

    /**
     * Reset all Criterias
     *
     * @return $this
     */
    public function resetCriteria(): static
    {
        $this->criteria = new Collection();

        return $this;
    }

    /**
     * Trigger method calls to the model
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call(string $method, array $arguments): mixed
    {
        $this->applyCriteria();
        $this->applyScope();

        return call_user_func_array([$this->model, $method], $arguments);
    }
}
