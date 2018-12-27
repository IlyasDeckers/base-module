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
                    $this->purchase[$fieldName], 
                    $this->query[$field]
                );
              } 
        });
    }
}