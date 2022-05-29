<?php

declare(strict_types=1);

namespace alvin0319\SizeShop\form;

use alvin0319\SizeShop\SizeShop;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function is_int;

class SizeForm implements Form{
	/** @var Player */
	protected Player $player;

	public function __construct(Player $player){
		$this->player = $player;
	}

	public function jsonSerialize() : array{
		return [
			"type" => "form",
			"title" => "§lSizeSystem - Master",
			"content" => "§f최대 크기: §d" . SizeShop::getInstance()->getSize($this->player, SizeShop::SIZE_BIG) . "\n§f최소 크기: §d" . SizeShop::getInstance()->getSize($this->player, SizeShop::SIZE_SMALL),
			"buttons" => [
				["text" => "§l* 나가기"],
				["text" => "§l* 크기 모드 선택하기"],
				["text" => "§l* 크기 커지기"],
				["text" => "§l* 크기 작아지기"]
			]
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(!is_int($data)){
			return;
		}
		switch($data){
			case 1:
				$player->sendForm(new SizeSelectForm($player));
				break;
			case 2:
				$player->sendForm(new SizeBuyForm(SizeShop::SIZE_BIG));
				break;
			case 3:
				$player->sendForm(new SizeBuyForm(SizeShop::SIZE_SMALL));
				break;
		}
	}
}