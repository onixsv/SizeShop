<?php

declare(strict_types=1);

namespace alvin0319\SizeShop\form;

use alvin0319\SizeShop\SizeShop;
use OnixUtils\OnixUtils;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function is_int;

class SizeSelectForm implements Form{
	/** @var Player */
	protected Player $player;

	public function __construct(Player $player){
		$this->player = $player;
	}

	public function jsonSerialize() : array{
		return [
			"type" => "form",
			"title" => "§lSizeSystem - Master",
			"content" => "현재 내 크기 모드는 " . SizeShop::convert(SizeShop::getInstance()->getMode($this->player)) . " 입니다.",
			"buttons" => [
				["text" => "§l* 나가기"],
				["text" => "§l* 커지기"],
				["text" => "§l* 기본으로 돌아가기"],
				["text" => "§l* 작아지기"]
			]
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(!is_int($data)){
			return;
		}
		switch($data){
			case 1:
				$mode = SizeShop::SIZE_BIG;
				break;
			case 2:
				$mode = SizeShop::SIZE_NORMAL;
				break;
			case 3:
				$mode = SizeShop::SIZE_SMALL;
				break;
			default:
				return;
		}
		SizeShop::getInstance()->setMode($player, $mode);
		OnixUtils::message($player, "내 크기 모드를 " . SizeShop::convert($mode) . "(으)로 바꿨습니다.");
	}
}