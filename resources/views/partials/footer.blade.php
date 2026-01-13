<footer class="bg-slate-900 text-gray-300 pt-16 pb-8border-t-4 border-orion-blue">
    <div class="container-custom">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12 mb-16">
            
            <div class="lg:col-span-2">
                <h3 class="text-white font-bold text-xl mb-8 relative inline-block">
                    Contact Us
                    <span class="absolute -bottom-2 left-0 w-12 h-1 bg-orion-blue"></span>
                </h3>

                <div class="flex flex-col md:flex-row gap-8">
                    <div class="flex-1 space-y-5">
                        <div class="space-y-1">
                            <h4 class="text-white font-bold text-lg uppercase tracking-wider">Orion Pharma Ltd.</h4>
                        </div>

                        <div class="flex items-start gap-4 group">
                            <div class="w-8 h-8 rounded bg-slate-800 flex items-center justify-center flex-shrink-0 group-hover:bg-orion-blue transition-colors">
                                <i class="fa-solid fa-location-dot text-orion-blue group-hover:text-white text-sm"></i>
                            </div>
                            <p class="text-sm leading-relaxed">
                                Orion House,<br>
                                153-154 Tejgaon Industrial Area,<br>
                                Dhaka-1208, Bangladesh
                            </p>
                        </div>

                        <div class="flex items-start gap-4 group">
                            <div class="w-8 h-8 rounded bg-slate-800 flex items-center justify-center flex-shrink-0 group-hover:bg-orion-blue transition-colors">
                                <i class="fa-solid fa-phone text-orion-blue group-hover:text-white text-sm"></i>
                            </div>
                            <div class="text-sm">
                                <p>+88 02 8870133</p>
                                <p>+88 02 8870134</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 group">
                            <div class="w-8 h-8 rounded bg-slate-800 flex items-center justify-center flex-shrink-0 group-hover:bg-orion-blue transition-colors">
                                <i class="fa-solid fa-fax text-orion-blue group-hover:text-white text-sm"></i>
                            </div>
                            <p class="text-sm">+88 02 8870130</p>
                        </div>

                        <div class="flex items-start gap-4 group">
                            <div class="w-8 h-8 rounded bg-slate-800 flex items-center justify-center flex-shrink-0 group-hover:bg-orion-blue transition-colors">
                                <i class="fa-solid fa-envelope text-orion-blue group-hover:text-white text-sm"></i>
                            </div>
                            <a href="mailto:orion@orion-group.net" class="text-sm hover:text-white transition-colors">orion@orion-group.net</a>
                        </div>
                    </div>

                    <div class="flex-1 min-h-[200px] rounded-xl overflow-hidden shadow-2xl border border-slate-700">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.4763034631333!2d90.40115855634214!3d23.766047574807324!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ee8ddd48510e1ef%3A0x40cf6706e9bda650!2sOrion%20Group!5e0!3m2!1sen!2sbd!4v1755749510694!5m2!1sen!2sbd"
                            width="100%" height="100%" style="border:0; filter: grayscale(0.3) invert(0.9) contrast(0.8);" allowfullscreen="" loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 lg:pl-8">
                <h3 class="text-white font-bold text-xl mb-8 relative inline-block">
                    Quick Links
                    <span class="absolute -bottom-2 left-0 w-12 h-1 bg-orion-blue"></span>
                </h3>
                <ul class="space-y-4">
                    @php $quickLinks = ['Home', 'About Us', 'Products', 'Investors', 'Career', 'Contact Us'];
                    @endphp
                    @foreach($quickLinks as $link)
                        <li>
                            <a href="#" class="text-sm hover:text-white hover:translate-x-2 transition-all flex items-center gap-3 group">
                                <span class="w-1.5 h-1.5 rounded-full bg-orion-blue group-hover:scale-150 transition-transform"></span>
                                {{ $link }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-1">
                <h3 class="text-white font-bold text-xl mb-8 relative inline-block">
                    Follow Us
                    <span class="absolute -bottom-2 left-0 w-12 h-1 bg-orion-blue"></span>
                </h3>
                <p class="text-sm mb-6 text-gray-400">Stay updated with our latest healthcare innovations.</p>
                <div class="flex flex-wrap gap-4">
                    <a href="#" class="w-11 h-11 rounded-lg bg-slate-800 flex items-center justify-center hover:bg-orion-blue hover:text-white transition-all transform hover:-translate-y-1 shadow-lg group">
                        <i class="fa-brands fa-facebook-f group-hover:scale-110"></i>
                    </a>
                    <a href="#" class="w-11 h-11 rounded-lg bg-slate-800 flex items-center justify-center hover:bg-orion-blue hover:text-white transition-all transform hover:-translate-y-1 shadow-lg group">
                        <i class="fa-brands fa-linkedin-in group-hover:scale-110"></i>
                    </a>
                    <a href="#" class="w-11 h-11 rounded-lg bg-slate-800 flex items-center justify-center hover:bg-orion-blue hover:text-white transition-all transform hover:-translate-y-1 shadow-lg group">
                        <i class="fa-brands fa-instagram group-hover:scale-110"></i>
                    </a>
                    <a href="#" class="w-11 h-11 rounded-lg bg-slate-800 flex items-center justify-center hover:bg-orion-blue hover:text-white transition-all transform hover:-translate-y-1 shadow-lg group">
                        <i class="fa-brands fa-x-twitter group-hover:scale-110"></i>
                    </a>
                </div>
            </div>

        </div>

        <div class="pt-8 border-t border-slate-800">
            <div class="text-center">
                <p class="text-[13px] text-gray-500 leading-relaxed">
                    Copyright &copy; {{ date('Y') }} <span class="text-gray-300 font-semibold tracking-wide">ORION</span>. All Rights Reserved. 
                    <span class="block md:inline mt-1 md:mt-0">Design & Developed by: <span class="text-orion-blue font-medium underline underline-offset-4 decoration-slate-700">Information Technology (IT), ORION.</span></span>
                </p>
            </div>
        </div>
    </div>
</footer>