<section class="relative w-full pt-[90px]">
    <div class="swiper homeSwiper w-full">
        <div class="swiper-wrapper">
            <div class="swiper-slide relative aspect-[10/4] overflow-hidden">
                <img src="{{ asset('storage/sliders/image1.jpg') }}" class="w-full h-full object-cover">
                class="w-full h-full object-cover" alt="Slide 1">
                <div class="absolute inset-0 bg-linear-to-r from-black/60 to-transparent flex items-center">
                    <div class="container mx-auto w-[90%] max-w-[1400px]">
                        <div class="max-w-2xl text-white space-y-4 slide-content">
                            <h2 class="text-4xl md:text-6xl font-bold leading-tight">Innovating for a <br><span
                                    class="text-blue-400">Healthier Tomorrow</span></h2>
                            <p class="text-lg opacity-90">Orion Pharma is committed to providing world-class healthcare
                                solutions through constant innovation and excellence.</p>
                            <div class="pt-4">
                                <a href="#"
                                    class="bg-orion-blue hover:bg-white hover:text-orion-blue text-white px-8 py-3 rounded-full font-semibold transition-all duration-300 inline-block">Explore
                                    Products</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="swiper-slide relative aspect-[10/4] overflow-hidden">
                <img src="{{ asset('storage/sliders/image2.jpg') }}" class="w-full h-full object-cover">
                class="w-full h-full object-cover" alt="Slide 2">
                <div class="absolute inset-0 bg-linear-to-r from-black/60 to-transparent flex items-center">
                    <div class="container mx-auto w-[90%] max-w-[1400px]">
                        <div class="max-w-2xl text-white space-y-4 slide-content">
                            <h2 class="text-4xl md:text-6xl font-bold leading-tight">Quality Excellence <br><span
                                    class="text-blue-400">Since 1965</span></h2>
                            <p class="text-lg opacity-90">State-of-the-art manufacturing facilities ensuring the highest
                                standards of pharmaceutical production.</p>
                            <div class="pt-4">
                                <a href="#"
                                    class="bg-orion-blue hover:bg-white hover:text-orion-blue text-white px-8 py-3 rounded-full font-semibold transition-all duration-300 inline-block">Our
                                    Heritage</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="swiper-button-next !text-white after:!text-2xl"></div>
        <div class="swiper-button-prev !text-white after:!text-2xl"></div>

        <div class="swiper-pagination"></div>
    </div>
</section>