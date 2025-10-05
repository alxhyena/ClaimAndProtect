<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\manager;

use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\world\World;
use xeonch\ClaimAndProtect\Main;

class LandManager
{

    private string $landDirectory;

    public function __construct()
    {
        $this->landDirectory = Main::getInstance()->getDataFolder() . "/lands/";
    }

    /**
     * Generates the next available land ID.
     *
     * @return int The next available land ID.
     */
    public function generateLandId(): int
    {
        $files = scandir($this->landDirectory);
        $ids = [];

        foreach ($files as $file) {
            if (preg_match('/^(\d+)\.json$/', $file, $matches)) {
                $ids[] = (int)$matches[1];
            }
        }

        if (empty($ids)) {
            return 1;
        }

        $maxId = max($ids);
        $missingIds = array_diff(range(1, $maxId), $ids);

        if (!empty($missingIds)) {
            return min($missingIds);
        }

        return $maxId + 1;
    }

    /**
     * Saves the land data to a JSON file.
     *
     * @param int $landId The land ID to save.
     * @param array $landData The land data to save.
     */
    public function saveLand(int $landId, array $landData): void
    {
        $filePath = $this->landDirectory . $landId . ".json";
        file_put_contents($filePath, json_encode($landData, JSON_PRETTY_PRINT));
    }

    /**
     * Reads land data from a JSON file.
     *
     * @param int $landId The land ID to read.
     * @return array|null The land data, or null if the file does not exist.
     */
    public function getLand(int $landId): ?array
    {
        $filePath = $this->landDirectory . $landId . ".json";
        if (file_exists($filePath)) {
            return json_decode(file_get_contents($filePath), true);
        }
        return null;
    }

    public function getAllLands(): array
    {
        $lands = [];
        $files = scandir($this->landDirectory);
        foreach ($files as $file) {
            if (preg_match('/^(\d+)\.json$/', $file)) {
                $landData = $this->getLand((int)preg_replace('/\.json$/', '', $file));
                if ($landData !== null) {
                    $lands[(int)preg_replace('/\.json$/', '', $file)] = $landData;
                }
            }
        }

        return $lands; // Return array of all lands
    }


    /**
     * Deletes the land based on the land ID.
     *
     * @param int $landId The land ID to delete.
     */
    public function deleteLand(int $landId): void
    {
        $filePath = $this->landDirectory . $landId . ".json";
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * Counts the number of lands created by a specific player.
     *
     * @param string $playerName The player's name.
     * @return int The number of lands owned by the player.
     */
    public function getLandCountByPlayer(string $playerName): int
    {
        $files = scandir($this->landDirectory);
        $count = 0;
        foreach ($files as $file) {
            if (preg_match('/^(\d+)\.json$/', $file)) {
                $landData = $this->getLand((int)preg_replace('/\.json$/', '', $file));
                if ($landData !== null && isset($landData['owner']) && $landData['owner'] === $playerName) {
                    $count++;
                }
            }
        }

        return $count;
    }

    public function isInArea(Position $pos): bool
    {
        $files = scandir($this->landDirectory);
        foreach ($files as $file) {
            if (preg_match('/^(\d+)\.json$/', $file)) {
                $landData = $this->getLand((int)preg_replace('/\.json$/', '', $file));
                if ($landData !== null && $landData['world'] === $pos->getWorld()->getFolderName()) {
                    list($firstX, $firstY, $firstZ) = explode(",", $landData['pos']['first']);
                    list($secondX, $secondY, $secondZ) = explode(",", $landData['pos']['second']);
                    $minX = min($firstX, $secondX);
                    $maxX = max($firstX, $secondX);
                    $minZ = min($firstZ, $secondZ);
                    $maxZ = max($firstZ, $secondZ);
                    if ($pos->getFloorX() >= $minX && $pos->getFloorX() <= $maxX) {
                        if ($pos->getFloorZ() >= $minZ && $pos->getFloorZ() <= $maxZ) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    public function getLandsByOwner(string $owner): array
    {
        $lands = [];
        $files = scandir($this->landDirectory);

        foreach ($files as $file) {
            if (preg_match('/^(\d+)\.json$/', $file)) {
                $landData = $this->getLand((int)preg_replace('/\.json$/', '', $file));
                if ($landData !== null && $landData['owner'] === $owner) {
                    $lands[(int)preg_replace('/\.json$/', '', $file)] = $landData;
                }
            }
        }

        return $lands;
    }

    /**
     * @return array
     */
    public function getLandsIn(Position $pos): array
    {
        $lands = [];
        $files = scandir($this->landDirectory);
        foreach ($files as $file) {
            if (preg_match('/^(\d+)\.json$/', $file)) {
                $landData = $this->getLand((int)preg_replace('/\.json$/', '', $file));
                if ($landData !== null && $landData['world'] === $pos->getWorld()->getFolderName()) {
                    list($firstX, $firstY, $firstZ) = explode(",", $landData['pos']['first']);
                    list($secondX, $secondY, $secondZ) = explode(",", $landData['pos']['second']);
                    $minX = min($firstX, $secondX);
                    $maxX = max($firstX, $secondX);
                    $minZ = min($firstZ, $secondZ);
                    $maxZ = max($firstZ, $secondZ);
                    if ($pos->getFloorX() >= $minX && $pos->getFloorX() <= $maxX) {
                        if ($pos->getFloorZ() >= $minZ && $pos->getFloorZ() <= $maxZ) {
                            $lands[(int)preg_replace('/\.json$/', '', $file)] = $landData;
                        }
                    }
                }
            }
        }
        return $lands;
    }


    public function teleportToLandCenter(Player $player, int $landId): void
    {
        $landData = $this->getLand($landId);
        $firstPos = $landData['pos']['first'];
        $secondPos = $landData['pos']['second'];
        list($firstX, $firstY, $firstZ) = explode(",", $firstPos);
        list($secondX, $secondY, $secondZ) = explode(",", $secondPos);

        $centerX = ($firstX + $secondX) / 2;
        $centerZ = ($firstZ + $secondZ) / 2;
        $world = $player->getWorld();
        $cnt = 0;
        $y = 128;
        for (; $y > 0; $y--) {
            $vec = new Vector3($centerX, $y, $centerZ);
            if ($world->getBlock($vec)->isSolid()) {
                $y++;
                break;
            }
            if ($cnt === 5) {
                break;
            }
            if ($y <= 0) {
                ++$cnt;
                ++$centerX;
                --$centerZ;
                $y = 128;
                continue;
            }
        }
        $centerPos = new Position($centerX + 0.5, $y + 0.1, $centerZ + 0.5, $world);
        $player->teleport($centerPos);
    }

    public function checkOverlap($startX, $endX, $startZ, $endZ, $world): bool
    {
        if ($world instanceof World) {
            $world = $world->getFolderName();
        }

        $startX = min($startX, $endX);
        $endX = max($startX, $endX);
        $startZ = min($startZ, $endZ);
        $endZ = max($startZ, $endZ);

        foreach ($this->getAllLands() as $landId => $landData) {
            $landWorld = $landData['world'];
            list($landX1, $landY1, $landZ1) = explode(",", $landData['pos']['first']);
            list($landX2, $landY2, $landZ2) = explode(",", $landData['pos']['second']);

            $startXLand = min($landX1, $landX2);
            $endXLand = max($landX1, $landX2);
            $startZLand = min($landZ1, $landZ2);
            $endZLand = max($landZ1, $landZ2);

            /* 
            var_dump("Checking overlap with: 
            New: startX=$startX, endX=$endX, startZ=$startZ, endZ=$endZ, world=$world
            Existing: startXLand=$startXLand, endXLand=$endXLand, startZLand=$startZLand, endZLand=$endZLand, landWorld=$landWorld");
            */

            if ($world === $landWorld) {
                if ($startX <= $endXLand && $endX >= $startXLand && $startZ <= $endZLand && $endZ >= $startZLand) {
                    /*var_dump("Overlap detected!");*/
                    return true;
                }
            }
        }
        return false;
    }
}
