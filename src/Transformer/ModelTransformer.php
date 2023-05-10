<?php namespace SOSTheBlack\Repository\Transformer;

use League\Fractal\TransformerAbstract;
use SOSTheBlack\Repository\Contracts\Transformable;

/**
 * Class ModelTransformer

 * @package SOSTheBlack\Repository\Transformer
 * @author Jean C. Garcia <garciasoftwares@gmail.com>
 */
class ModelTransformer extends TransformerAbstract
{
    public function transform(Transformable $model): array
    {
        return $model->transform();
    }
}
