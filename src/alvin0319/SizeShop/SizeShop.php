<?php

declare(strict_types=1);

namespace alvin0319\SizeShop;

use OnixUtils\OnixUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function json_decode;
use function json_encode;

class SizeShop extends PluginBase{
	use SingletonTrait;

	public const SIZE_BIG = "big";
	public const SIZE_SMALL = "small";
	public const SIZE_NORMAL = "normal";

	protected array $data = [];

	protected function onLoad() : void{
		self::setInstance($this);
	}

	protected function onEnable() : void{
		if(file_exists($file = $this->getDataFolder() . "SizeData.json")){
			$this->data = json_decode(file_get_contents($file), true);
		}
		$this->getServer()->getPluginManager()->registerEvent(PlayerJoinEvent::class, function(PlayerJoinEvent $event) : void{
			$player = $event->getPlayer();
			if(!$this->hasData($player)){
				$this->createData($player);
			}
			$this->applySize($player);
		}, EventPriority::NORMAL, $this);
	}

	protected function onDisable() : void{
		file_put_contents($this->getDataFolder() . "SizeData.json", json_encode($this->data));
	}

	public function hasData(Player $player) : bool{
		return isset($this->data[$player->getName()]);
	}

	public function createData(Player $player) : void{
		$this->data[$player->getName()] = [
			"small" => 0,
			"big" => 0,
			"smallLimit" => 0.6,
			"bigLimit" => 1.5,
			"select" => self::SIZE_NORMAL
		];
	}

	public function addSize(Player $player, string $sizeMode, float $size) : void{
		if(!$this->hasData($player)){
			return;
		}
		if(!$this->canAddSize($player, $sizeMode)){
			return;
		}
		$this->data[$player->getName()][$sizeMode] += $size;
		$this->applySize($player);
	}

	public function canAddSize(Player $player, string $size) : bool{
		return $this->data[$player->getName()][$size] < $this->data[$player->getName()][$size . "Limit"];
	}

	public function getSize(Player $player, string $size) : float{
		return $this->data[$player->getName()][$size];
	}

	public function getMode(Player $player) : string{
		return $this->data[$player->getName()]["select"];
	}

	public function getLimit(Player $player, string $mode) : float{
		return $this->data[$player->getName()][$mode . "Limit"];
	}

	public function setMode(Player $player, string $mode) : void{
		$this->data[$player->getName()]["select"] = $mode;
		$this->applySize($player);
	}

	public function applySize(Player $player) : void{
		switch($this->data[$player->getName()]["select"]){
			default:
			case self::SIZE_NORMAL:
				$player->setScale(1);
				break;
			case self::SIZE_BIG:
				$player->setScale(1 + $this->getSize($player, self::SIZE_BIG));
				break;
			case self::SIZE_SMALL:
				$player->setScale(1 - $this->getSize($player, self::SIZE_SMALL));
				break;
		}
	}

	public static function convert(string $type) : string{
		switch($type){
			case self::SIZE_SMALL:
				return "작아지기";
			case self::SIZE_BIG:
				return "커지기";
			case self::SIZE_NORMAL:
				return "기본";
			default:
				return "알 수 없음";
		}
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(!$sender instanceof Player){
			return false;
		}
		//$sender->sendForm(new SizeForm($sender));
		OnixUtils::message($sender, "크기 상점은 아직 이용하실 수 없습니다.");
		return true;
	}
}