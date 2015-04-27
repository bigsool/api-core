<?php


namespace Core\Doctrine\Hydrator;


use Doctrine\ORM\Internal\Hydration\ArrayHydrator;

class ArrayIdHydrator extends ArrayHydrator {

    /**
     * {@inheritdoc}
     */
    protected function hydrateRowData (array $row, array &$result) {

        $rowOnlyWithId = [];
        foreach ($row as $key => &$value) {
            if (!strncmp($key, 'id', 2)) {
                $rowOnlyWithId[$key] = $value;
            }
        }

        parent::hydrateRowData($rowOnlyWithId, $result);

    }

}