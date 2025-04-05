<script setup>
const props = defineProps({
    sliders:Array
});
console.log(props.sliders)
</script>
<template>
          <section class="ul-banner">
            <!-- top -->
            <div class="top">
                <div class="row gy-4 row-cols-md-2 row-cols-1 align-items-center flex-column-reverse flex-md-row">
                    <!-- banner text -->
                    <div class="col">
                        <div class="ul-banner-txt">
                            <div v-if="sliders" class="ul-banner-txt-slider swiper">
                                <div class="swiper-wrapper">


                                    <!-- single slide -->
                                    <div v-for="slider in sliders" :key="slider.id" class="swiper-slide">
                                        <div class="ul-banner-txt-slide">
                                            <div class="wow animate__fadeInUp">
                                                <span class="ul-banner-sub-title">{{ slider.title }}</span>
                                                <h1 class="ul-banner-title"><img :src="slider ? slider.image : 'assets/img/banner-txt-img.jpg'"> Restaurants/Cafes</h1>
                                                <p class="ul-banner-descr">{{ slider.desc }}</p>
                                                <div class="ul-banner-btns">
                                                    <a href="shop.html" class="ul-btn">order now <i class="flaticon-right"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- img -->
                    <div class="col">
                        <div class="ul-banner-img wow animate__fadeInRight">
                            <div class="ul-banner-img-slider swiper">
                                <div class="swiper-wrapper">
                                    <!-- single slide -->
                                    <div class="swiper-slide">
          
                                        <img
                    :src="
                        slider
                            ? `/storage/images/listing/default.jpeg`
                            : '/assets/img/banner-img-1.png'
                    "
                    class=""
                    alt=""
                />
                                    </div>

                                    <!-- single slide -->
                                    <div class="swiper-slide">
                                        <img
                    :src="
                        slider
                            ? slider.image
                            : 'assets/img/banner-img-1.png'
                    "
                    class=""
                    alt=""
                />
                                    </div>

                                    <!-- single slide -->
                                    <div class="swiper-slide">
                                        <img
                    :src="
                        slider
                            ? slider.image
                            : '/assets/img/banner-img-1.png'
                    "
                    class=""
                    alt=""
                />
                                    </div>
                                </div>
                            </div>

                            <div class="ul-banner-img-slider-dots"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- bottom -->
            <div class="bottom">
                <div class="ul-banner-txt-slider-pagination"></div>
                <div class="ul-banner-txt-slider-nav">
                    <button class="prev"><i class="flaticon-left-arrow"></i></button>
                    <button class="next"><i class="flaticon-right-arrow-3"></i></button>
                </div>
            </div>

            <!-- vector -->
            <div class="ul-banner-vectors">

                <img
                    :src="
                        slider
                            ? `/storage/images/banner-vector-2.png`
                            : '/storage/images/banner-vector-2.png'
                    "
                  class="vector-1 wow animate__fadeInLeft"
                />
                <img
                    :src="
                        slider
                            ? `/storage/images/banner-vector-2.png`
                            : '/storage/images/banner-vector-2.png'
                    "
                  class="vector-2 wow animate__fadeInLeft"
                />
                <img
                    :src="
                        slider
                            ? `/storage/images/banner-vector-2.png`
                            : '/storage/images/banner-vector-2.png'
                    "
                  class="vector-3 wow animate__fadeInRight"
                />
            </div>
        </section>
</template>



<style scoped>

</style>