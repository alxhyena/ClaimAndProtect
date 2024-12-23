<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\utils;

use pocketmine\world\Position;
use pocketmine\world\World;

class Math {

    /**
     * Calculate the area between two positions
     *
     * @param string $pos1 The first coordinate in 'x,y,z' format
     * @param string $pos2 The second coordinate in 'x,y,z' format
     * @return int The area between pos1 and pos2
     */
    public static function calculateArea(string $pos1, string $pos2): int
    {
        list($x1,, $z1) = explode(",", $pos1);
        list($x2,, $z2) = explode(",", $pos2);

        $length = abs((int)$x2 - (int)$x1) + 1;
        $width = abs((int)$z2 - (int)$z1) + 1;

        return $length * $width;
    }

    /**
     * Convert Position object to a string (x,y,z,world)
     *
     * @param Position $pos
     * @return string
     */
    public static function convertPosToString(Position $pos): string
    {
        return $pos->getX() . "," . $pos->getY() . "," . $pos->getZ() . "," . $pos->getWorld()->getFolderName();
    }

    /**
     * Convert a string to a Position object
     *
     * @param string $posString
     * @param World $world
     * @return Position|null
     */
    public static function convertStringToPos(string $posString, World $world): ?Position
    {
        $parts = explode(",", $posString);
        if (count($parts) !== 4) {
            return null;
        }
        $x = intval($parts[0]);
        $y = intval($parts[1]);
        $z = intval($parts[2]);
        $worldName = $parts[3];
        if ($world->getFolderName() !== $worldName) {
            return null;
        }
        return new Position($x, $y, $z, $world);
    }

    /**
     * Calculates the price after discount based on a percentage with a range of 10% to 100%.
     *
     * @param int $price Original price before discount
     * @param int $discountPercentage Discount percentage (1, 10, 20, 30, ..., 100)
     * @return float Price after discount
     */
    public static function applyDiscountWithPercentage(int $price, int $discountPercentage): int|float
    {
        if ($discountPercentage < 1) {
            $discountPercentage = 1;
        } elseif ($discountPercentage > 100) {
            $discountPercentage = 100;
        }
        $discountedPrice = $price * ($discountPercentage / 100);
        return $discountedPrice;
    }
}