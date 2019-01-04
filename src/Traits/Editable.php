<?php
namespace Clockwork\Base\Traits;

trait Editable {
    public function formatMessage()
    {
        $fields = collect([
            'message', 'subject'
        ]);

        foreach ($this->variables as $variable) {
            $this->replace($fields, $variable);
        }
    }

    private function replace($fields, $variable)
    {
        $fields->each(function ($field) use ($variable) {
            $toReplace = '{{ ' . $variable . ' }}';
            $fieldName = str_replace('-', '_', $variable);
            $hasVariable = strpos(
                $this->query[$field], $toReplace
            );

            if($hasVariable !== false) {
                $this->query[$field] = str_replace(
                    $toReplace,
                    $this->setFieldName($fieldName),
                    $this->query[$field]
                );
            } 
        });
    }

    private function setFieldName($fieldName)
    {
        if ($fieldName === 'total_ex') {
            $result = $this->purchase['total'];
        } elseif ($fieldName === 'total_inc') {
            $result = $this->purchase->total_incl;
        } else {
            $result = $this->purchase[$fieldName];
        }

        return $result;
    }
}