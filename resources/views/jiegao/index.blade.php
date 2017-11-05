@extends('jiegao.layouts.app')
@section('css')
    <link rel="stylesheet" type="text/css" href="{!! cdn('jiegao/lib/slick/slick.min.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! cdn('jiegao/lib/slick/slick-theme.min.css') !!}">
@endsection
@section('js')
    <script type="text/javascript" src="{!! cdn('jiegao/lib/jquery/jquery.min.js') !!}"></script>
    <script type="text/javascript" src="{!! cdn('jiegao/lib/slick/slick.min.js') !!}"></script>
@endsection
@section('keywords'){{ setting('default_keywords') }}@endsection
@section('description'){{ setting('default_description') }}@endsection
@section('title'){{ setting('site_name') }}@endsection

@section('content')
    @widget('navigation_bar')
    @widget('banner', ['type' => 'top_pic'])
    <div class="content">
        <div class="container">
            <div class="about">
                <div class="header">
                    @php
                        $categoryRepository = app(App\Repositories\CategoryRepository::class);
                        $about = $categoryRepository->findByCateName('关于捷高');
                    @endphp
                    <h2>{{$about->cate_name}}</h2>
                </div>
                <div class="info">
                    <img src="{!! cdn('jiegao/images/about.png') !!}">
                    <p class="text">
                        我们的流线式网页布局设计方案和可视化图文内容编辑模式让网站制作和维护成为一件轻松惬意的事。无论您是普通互联页设计出最制作人页设计出最制作人作人页设计出最制作人页设计出最制作人作人计出最制作人作人页设人
                        我们的流线式网页布局设计方案和可视化图文内容编辑模式让网站制作和维护成为一件轻松惬意的事。无论您是普通互联页设计出最制作人页设计出最制作人作人页设计出最制作人页设计出最制作人作人计出最制作人作人页设人
                    </p>
                </div>
            </div>
            @widget('corporate_environment_banner')
        </div>
    </div>
    @php
        $categoryRepository = app(App\Repositories\CategoryRepository::class);
        $products = $categoryRepository->findByCateName('产品中心');
    @endphp
    <div class="prod">
        <div class="container">
            <div class="header">
                <h2>{{$products->cate_name}}</h2>
                <div class="line"></div>
            </div>
            <div class="main">
                @foreach(Facades\App\Widgets\PostList::mergeConfig(['category'=>$products])->getData()['posts'] as $post)
                    <div class="product_item">
                        <a href="{!! $post->getPresenter()->url() !!}">
                            <img src="{!! image_url($post->cover) !!}" alt="">
                            <div class="mask_wrap">
                                <span>{!! $post->title !!}</span>
                                <div class="mask"></div>
                            </div>
                        </a>
                    </div>
                @endforeach

            </div>
            <div class="more">
                <a class="btn more_btn" {!! $products->getPresenter()->linkAttribute() !!}>查看更多</a>
            </div>
        </div>
    </div>
    @include('jiegao.layouts.particals.footer')
@endsection

@push('js')
    <script>
      $(function () {
        var $banner = $('#banner')
        if ($banner.children().length == 0)
          return
        $banner.slick({
          dots: true,
          infinite: true,
          centerMode: true,
          variableWidth: true,
          autoplay: true,
          autoplaySpeed: 5000,
          slidesToShow: 3,
          slidesToScroll: 3,
          arrows: false
        });
        var $envBanner = $('#env_banner')
        if ($envBanner.children().length == 0)
          return
        $envBanner.slick({
          dots: false,
          infinite: true,
          centerMode: true,
          variableWidth: true,
          autoplay: true,
          autoplaySpeed: 5000,
          slidesToShow: 3,
          slidesToScroll: 3,
          arrows: true
        })
        $envBannerTitle = $('#env_banner_title');
        setEnvCurrentText();
        $envBanner.on('afterChange',function(event, slick, currentSlide){
          setEnvCurrentText();
        })
        function setEnvCurrentText () {
          var $currentText = $envBanner.find('.slick-current .text');
          $envBannerTitle.html($currentText.html());
        }
      })

    </script>
@endpush