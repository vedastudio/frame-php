<?php

namespace Frame\Validator;

use libphonenumber\PhoneNumberUtil;

trait RulesTrait
{
    public function required(): self
    {
        if ((is_array($this->value) && empty($this->value)) || empty(trim($this->value))) {
            $this->addError(__FUNCTION__);
        }
        return $this;
    }

    public function string(): self
    {
        if (!is_string($this->value)) {
            $this->addError(__FUNCTION__);
        }
        return $this;
    }

    public function numeric(): self
    {
        if (!is_numeric($this->value)) {
            $this->addError(__FUNCTION__);
        }
        return $this;
    }

    public function pattern(string $regex): self
    {
        $pattern = '/^(' . $regex . ')$/u';
        if (!preg_match($pattern, $this->value)) {
            $this->addError(__FUNCTION__, ['{pattern}' => $regex]);
        }
        return $this;
    }

    public function phone(): self
    {
        if (PhoneNumberUtil::isViablePhoneNumber($this->value)) {
            $phoneUtil = PhoneNumberUtil::getInstance();
            $numberProto = $phoneUtil->parse($this->value, $this->defaultRegion);
            if ($phoneUtil->isValidNumber($numberProto) === false) {
                $this->addError(__FUNCTION__);
            }
        } else {
            $this->addError(__FUNCTION__);
        }
        return $this;
    }
}