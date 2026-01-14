<footer class="bg-orion-blue text-gray-100 pt-8 pb-[10px]">
    <div class="container mx-auto w-[90%] max-w-[1400px]">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12 mb-8">

            <div class="lg:col-span-2">
                <h1 class="border-b-2 border-white/30 pb-4">Contact Us</h1>

                <div class="flex flex-col md:flex-row gap-8">
                    <div class="flex-1 space-y-5">
                        <div class="space-y-1 opacity-90">
                            <h1 class="!normal-case tracking-normal mb-2">Orion Pharma Ltd.</h1>
                        </div>

                        <div class="flex items-start gap-4 opacity-90 hover:opacity-100 transition-opacity group">
                            <i class="fa-solid fa-location-dot text-white mt-1 w-5 text-center"></i>
                            <p class="text-base leading-relaxed">
                                Orion House,<br>
                                153-154 Tejgaon Industrial Area,<br>
                                Dhaka-1208, Bangladesh
                            </p>
                        </div>

                        <div class="flex items-start gap-4 opacity-90 hover:opacity-100 transition-opacity group">
                            <i class="fa-solid fa-phone text-white mt-1 w-5 text-center"></i>
                            <div class="text-base">
                                <p>+88 02 8870133</p>
                                <p>+88 02 8870134</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 opacity-90 hover:opacity-100 transition-opacity group">
                            <i class="fa-solid fa-fax text-white mt-1 w-5 text-center"></i>
                            <p class="text-base">+88 02 8870130</p>
                        </div>

                        <div class="flex items-start gap-4 opacity-90 hover:opacity-100 transition-opacity group">
                            <i class="fa-solid fa-envelope text-white mt-1 w-5 text-center"></i>
                            <a href="mailto:orion@orion-group.net"
                                class="text-base hover:text-white transition-all">orion@orion-group.net</a>
                        </div>
                    </div>

                    <div class="flex-1 rounded-lg overflow-hidden border border-white/10 min-h-[200px] opacity-90">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.4763034631333!2d90.40115855634214!3d23.766047574807324!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ee8ddd48510e1ef%3A0x40cf6706e9bda650!2sOrion%20Group!5e0!3m2!1sen!2sbd!4v1755749510694!5m2!1sen!2sbd"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 lg:pl-8">
                <h1 class="border-b-2 border-white/30 pb-4">Quick Links</h1>

                <ul class="space-y-4">
                    @php 
                        $quickLinks = ['Home', 'About Us', 'Products', 'Investors', 'CSR', 'News', 'Contact Us'];
                    @endphp
                    @foreach($quickLinks as $link)
                        <li>                        <a href="#" class="text-base opacity-90 hover:opacity-100 hover:translate-x-1 transition-all inline-block hover:text-white">
                            {{ $link }}
                                </a>
                            </li>
                    @endforeach
                </ul>
            </div>
 
            <div class="lg:col-span-1">
<h1 class="border-b-2 border-white/30 pb-4">Follow Us</h1>


                                
                <p class="text-base mb-6 text-gray-200 opacity-90">Stay updated wit
 h                       our latest healthcare innovations.</p>
                <div class="flex flex-wrap gap-4">
                    <a href="https://facebook.com/OrionPharmaLtdbd" target="_blank" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center hover:bg-white hover:text-orion-blue transition-all group shadow-sm hover:shadow-lg">
                        <i class="fa-brands fa-facebook-f text-base"></i>

                                           </a>
                    <a href="https://linkedin.com/company/orion-group.bd" target="_blank" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center hover:bg-white hover:text-orion-blue transition-all group shadow-sm hover:shadow-lg">
                        <i class="fa-brands fa-linkedin-in text-base"></i>

                                           </a>
                    <a href="https://youtube.com/@orionconglomerate" target="_blank" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center hover:bg-white hover:text-orion-blue transition-all group shadow-sm hover:shadow-lg">
                        <i class="fa-brands fa-youtube text-base"></i>
                    </a>
                </div>
            </div>

        </div>

        <div class="pt-[10px] border-t border-white/30">
            <div class="text-center">
                <p class="text-sm text-gray-300 leading-relaxed">
                    Copyright &copy; {{ date('Y') }} 
                    <span class="text-white font-semibold tracking-wide opacity-90">ORION</span>. All Rights Reserved. 
                    <span class="block md:inline mt-1 md:mt-0">Design & Developed by: 
                        <span class="text-white font-medium opacity-90">Information Technology (IT), ORION.</span>
                    </span>
                </p>
            </div>
        </div>
    </div>
</footer>