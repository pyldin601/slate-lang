<?php

function bas()
{
    return function () {
        return function () {
            return function () {
                throw new Exception;
            };
        };
    };
}
