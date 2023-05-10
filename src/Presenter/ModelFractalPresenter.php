<?php

namespace SOSTheBlack\Repository\Presenter;

use Exception;
use League\Fractal\TransformerAbstract;
use SOSTheBlack\Repository\Transformer\ModelTransformer;

/**
 * Class ModelFractalPresenter
 * @package SOSTheBlack\Repository\Presenter
 * @author Jean C. Garcia <garciasoftwares@gmail.com>
 */
class ModelFractalPresenter extends FractalPresenter
{

    /**
     * Transformer
     *
     * @return ModelTransformer
     *
     * @throws Exception
     */
    public function getTransformer(): TransformerAbstract
    {
        if (!class_exists('League\Fractal\Manager')) {
            throw new Exception("Package required. Please install: 'composer require league/fractal' (0.12.*)");
        }

        return new ModelTransformer();
    }
}
