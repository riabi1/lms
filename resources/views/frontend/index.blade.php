@extends('frontend.master')

@section('title')
    Easy Learning
@endsection

@section('home')
    <!--================================
             START HERO AREA
    =================================-->
    @include('frontend.home.hero-area')
    <!--================================
            END HERO AREA
    =================================-->

    <!--======================================
            START FEATURE AREA
    ======================================-->
    @include('frontend.home.feature-area')
    <!--======================================
           END FEATURE AREA
    ======================================-->

    <!--======================================
            START CATEGORY AREA
    ======================================-->
    @include('frontend.home.category-area')
    <!--======================================
            END CATEGORY AREA
    ======================================-->

    <!--======================================
            START COURSE AREA
    ======================================-->
    @include('frontend.home.courses-area')
    <!--======================================
            END COURSE AREA
    ======================================-->
    @include('frontend.home.courses-area-two')
    <!--================================
             START TESTIMONIAL AREA
    =================================-->
    
    @include('frontend.home.testimonial-area')
    <!--================================
            END TESTIMONIAL AREA
    =================================-->
    @include('frontend.home.about-area')
    <!-- ================================
           START BLOG AREA
    ================================= -->
    @include('frontend.home.blog-area')
    <!-- ================================
           END BLOG AREA
    ================================= -->
@endsection