<?php

namespace tpext\builder\common;

use tpext\builder\inface\Renderable;
use tpext\think\View;

class Swiper extends Widget implements Renderable
{
    /**
     * Undocumented variable
     *
     * @var View
     */
    protected $content;

    protected $partial = false;

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function partial($val = true)
    {
        $this->partial = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $arr [['title'=>title1,'image'=>image1],['title'=>title2,'image'=>image2],...] or [image1, image2,...]
     * @return $this
     */
    public function images($arr)
    {
        $list = [];
        foreach ($arr as $k => $v) {
            if (isset($v['image'])) {
                $list[] = ['title' => $v['title'] ?? $k, 'image' => $v['image']];
            } else {
                $list[] = ['title' => $k, 'image' => $v];
            }
        }
        $tpl = '<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
        {volist name="list" id="vo"}
          <li data-target="#carouselExampleIndicators" data-slide-to="{$key}" {if condition="$key eq 0"}class="active"{/if}></li>
        {/volist}
        </ol>
        <div class="carousel-inner">
          {volist name="list" id="vo"}
          <div class="item {if condition="$key eq 0"}active{/if}"><img style="width:100%;height:380px;" src="{$vo.image}" alt="{$vo.title}"></div>
          {/volist}
        </div>
        <a class="left carousel-control" href="#carouselExampleIndicators" role="button" data-slide="prev"><span class="icon-left-open-big icon-prev" aria-hidden="true"></span><span class="sr-only">上一个</span></a>
        <a class="right carousel-control" href="#carouselExampleIndicators" role="button" data-slide="next"><span class="icon-right-open-big icon-next" aria-hidden="true"></span><span class="sr-only">下一个</span></a>
      </div>';

        $this->content = new View($tpl);

        $this->content->assign(['list' => $list])->isContent(true);
        return $this;
    }

    public function beforRender()
    {
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string|View
     */
    public function render()
    {
        if ($this->partial) {
            return $this->content;
        }

        return $this->content->getContent();
    }

    public function __toString()
    {
        $this->partial = false;
        return $this->render();
    }

    public function destroy()
    {
        $this->content = null;
    }
}
