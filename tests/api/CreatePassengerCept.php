<?php

if (!empty($scenario)) {
    $I = new ApiTester($scenario);
    $passenger = $I->getNewPassenger();
}
