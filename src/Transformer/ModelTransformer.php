<?php namespace SOSTheBlack\Repository\Transformer;

use League\Fractal\TransformerAbstract;
use SOSTheBlack\Repository\Contracts\Transformable;

/**
 * Class ModelTransformer
 * @package SOSTheBlack\Repository\Transformer
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class ModelTransformer extends TransformerAbstract
{
    public function transform(Transformable $model)
    {
        return $model->transform();
    }
}
