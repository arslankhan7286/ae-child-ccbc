<?php

namespace Ardent;

class Pager extends \Ardent\BaseObject {

	protected $_config = [
		'prev' => true,
		'next' => true,
		'page' => 1,
		'pages' => null,
		'format' => "",
                'max' => null,
                'next' => 'Next',
                'prev' => 'Prev'
	];

	private function _parse_link($link, $page) {
		return str_replace('{{page}}', $page, $link);
	}

	public function getLinks() {
		if ($this->pages <= 1) { return []; }
                $max = $this->max;
                $sp = 1;
                $links = [];
                if($this->page > 1){
                    $links[] = (string) $this->_get_prev_link();
                }
                if($max && $max < $this->pages){
                    if($this->page >= $max){
                        $links[] = $this->_get_link(1, 1);
                        $links[] = '...';
                    }
                    if($this->page < $max){
                        $sp = 1;
                    }else if($this->page >= ($this->pages - floor($max / 2)) ){
                        $sp = $this->pages - $max + 1;
                    }elseif($this->page >= $max){
                        $sp = $this->page  - floor($max/2);
                    }

                    for ($p = $sp; $p <= $sp+$max-1; $p++) {
                        if($p > $this->pages){
                            continue;
                        }
                        $links[] = $this->_get_link($p, $p);
                    }
//                    var_dump($this->page, $this->pages, ($this->pages - floor($max / 2)), $p);
                    if(($this->page < $max && $this->page <= ($this->pages - floor($max / 2))) || $this->page < ($this->pages - floor($max / 2))){
                        if($p < $this->pages){
                            $links[] = '...';
                        }
                        $links[] = $this->_get_link($this->pages, $this->pages);
                    }
                }else{
                    for ($p = 1; $p <= $this->pages; $p++) {
                        $links[] = $this->_get_link($p, $p);
                    }
                }
                if($this->page < $this->pages){
                    $links[] = $this->_get_next_link();
                }
		return $links;
	}

	private function _get_prev_link() {
		if (!$this->prev) {
			return null;
		}
                $link = $this->_get_link($this->page - 1, $this->prev);
                $link->class = 'prev-link';
		return $link;
	}

	private function _get_next_link() {
		if (!$this->next || $this->page == $this->pages) {
			return null;
		}
		$link = $this->_get_link($this->page + 1, $this->next);
                $link->class = 'next-link';
		return $link;
	}

	private function _get_link($page, $text) {

		$url = $this->_parse_link($this->format, $page);
		if ($page == $this->page) {
			$link = \Ardent\Html::get([
						'type' => 'span',
						'text' => $text ?: $url
			]);
		} else {
			$link = \Ardent\Html::get([
						'type' => 'a',
						'href' => $url,
						'text' => $text ?: $url
			]);
		}
		if ($page == $this->page) {
			$link->class = 'active';
		}
                $link->{'data-pagenumber'} = $page;
		return $link;
	}

}
