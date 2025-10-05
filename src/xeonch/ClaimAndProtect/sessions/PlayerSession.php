<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\sessions;

use pocketmine\player\Player;

class PlayerSession
{

    /**
     * The player instance associated with this object.
     * Represents the player interacting with the system.
     */
    private Player $player;

    /**
     * Stores active player sessions.
     * This property maps player identifiers to their respective session data.
     *
     * @var array<string, array>
     */
    private array $sessions = [];



    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->init();
    }

    public function init()
    {
        $playerName = $this->player->getName();
        $this->sessions[$playerName] = [
            'first' => false,
            'second' => false,
            'first-pos' => null,
            'second-pos' => null
        ];
        // var_dump($this->sessions[$playerName]); // Debug
    }

    public function isFirstSession(): bool
    {
        $playerName = $this->player->getName();
        // var_dump($this->sessions[$playerName]); // Debug
        return isset($this->sessions[$playerName]['first']);
    }

    public function isSecondSession(): bool
    {
        $playerName = $this->player->getName();
        return isset($this->sessions[$playerName]['second']);
    }

    public function setFirstSession(bool $value = true)
    {
        $playerName = $this->player->getName();
        $this->sessions[$playerName]['first'] = $value;
        // var_dump($this->sessions[$playerName]);
    }

    public function setSecondSession(bool $value = true)
    {
        $playerName = $this->player->getName();
        $this->sessions[$playerName]['second'] = $value;
    }

    public function unSetFirstSession()
    {
        $playerName = $this->player->getName();
        if ($this->isFirstSession()) {
            unset($this->sessions[$playerName]['first']);
        }
    }

    public function unSetSecondSession()
    {
        $playerName = $this->player->getName();
        if ($this->isSecondSession()) {
            unset($this->sessions[$playerName]['second']);
        }
    }

    public function setFirstPos($pos)
    {
        $playerName = $this->player->getName();
        if ($this->isFirstSession()) {
            // var_dump($this->sessions[$playerName]);
            $this->sessions[$playerName]['first-pos'] = $pos;
        }
    }

    public function setSecondPos($pos)
    {
        $playerName = $this->player->getName();
        if ($this->isSecondSession()) {
            // var_dump($this->sessions[$playerName]);
            $this->sessions[$playerName]['second-pos'] = $pos;
        }
    }

    public function getFirtsSession(): bool
    {
        $playerName = $this->player->getName();
        if ($this->isFirstSession()) {
            return $this->sessions[$playerName]['first'] ?? false;
        }
        return false;
    }

    public function getSecondSession(): bool
    {
        $playerName = $this->player->getName();
        if ($this->isSecondSession()) {
            return $this->sessions[$playerName]['second'] ?? false;
        }
        return false;
    }

    public function getFirstPos(): ?string
    {
        $playerName = $this->player->getName();
        return $this->sessions[$playerName]['first-pos'] ?? null;
    }

    public function getSecondPos(): ?string
    {
        $playerName = $this->player->getName();
        return $this->sessions[$playerName]['second-pos'] ?? null;
    }
}
