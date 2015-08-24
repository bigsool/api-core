<?php

namespace Core\Interaction;


interface Interaction {

    /**
     * @return string
     */
    public function getType ();

    /**
     * @return string
     */
    public function getTopic ();

    /**
     * @return string
     */
    public function getMessage ();

    /**
     * @return array
     */
    public function toArray ();

}