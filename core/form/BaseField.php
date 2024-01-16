<?php

namespace app\core\form;

use app\core\Model;

abstract class BaseField
{
    public Model $model;
    public string $attribute;
    public bool $isReadOnly = false;
    public bool $isHidden = false;

    /**
     * @param Model $model
     * @param string $attribute
     */
    public function __construct(Model $model, string $attribute)
    {
        $this->model = $model;
        $this->attribute = $attribute;
    }

    public function readOnlyField(): BaseField
    {
        $this->isReadOnly = true;
        return $this;
    }

    public function hiddenField(): BaseField
    {
        $this->isHidden = true;
        return $this;
    }

    abstract public function renderInput() : string;

    public function __toString()
    {
        return sprintf(
            '<div class="mb-3 form-group">
                        %s
                        <div class="invalid-feedback">
                            %s
                        </div>
                    </div>
                    ',
            $this->renderInput(),
            $this->model->getFirstError($this->attribute));
    }
}