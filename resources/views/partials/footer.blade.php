<footer class="bg-slate-900 text-gray-300 pt-16 pb-8">
    <div class="container-custom">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">

            <div class="space-y-6">
                <a href="{{ url('/') }}" class="inline-block">
                    <img src="{{ asset('images/logo-white.svg') }}" alt="ORION" class="h-10 w-auto brightness-0 invert">
                </a>
                <p class="text-sm leading-relaxed text-gray-400">
                    ORION Pharma Limited is one of the premier pharmaceutical companies in Bangladesh,
                    dedicated to improving the health and well-being of people through innovative
                    and quality medicines.
                </p>
                <div class="flex space-x-4">
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-orion-blue hover:text-white transition-all">
                        <i class="fa-brands fa-facebook-f text-sm"></i>
                    </a>
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-orion-blue hover:text-white transition-all">
                        <i class="fa-brands fa-linkedin-in text-sm"></i>
                    </a>
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-orion-blue hover:text-white transition-all">
                        <i class="fa-brands fa-x-twitter text-sm"></i>
                    </a>
                </div>
            </div>

            <div>
                <h3 class="text-white font-bold text-lg mb-6 relative inline-block">
                    Quick Links
                    <span class="absolute -bottom-2 left-0 w-8 h-1 bg-orion-blue"></span>
                </h3>
                <ul class="space-y-4">
                    <li><a href="/home"
                            class="text-sm hover:text-white hover:translate-x-2 transition-all inline-block flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-[10px] text-orion-blue opacity-0 group-hover:opacity-100 transition-all"></i>
                            Home
                        </a></li>
                    <li><a href="/about"
                            class="text-sm hover:text-white hover:translate-x-2 transition-all inline-block flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-[10px] text-orion-blue opacity-0 group-hover:opacity-100 transition-all"></i>
                            About Us
                        </a></li>
                    <li><a href="/products"
                            class="text-sm hover:text-white hover:translate-x-2 transition-all inline-block flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-[10px] text-orion-blue opacity-0 group-hover:opacity-100 transition-all"></i>
                            Products
                        </a></li>
                    <li><a href="/contact"
                            class="text-sm hover:text-white hover:translate-x-2 transition-all inline-block flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-[10px] text-orion-blue opacity-0 group-hover:opacity-100 transition-all"></i>
                            Contact
                        </a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-bold text-lg mb-6 relative inline-block">
                    Corporate
                    <span class="absolute -bottom-2 left-0 w-8 h-1 bg-orion-blue"></span>
                </h3>
                <ul class="space-y-4">
                    <li><a href="#"
                            class="text-sm hover:text-white hover:translate-x-2 transition-all inline-block flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-[10px] text-orion-blue opacity-0 group-hover:opacity-100 transition-all"></i>
                            Investors
                        </a></li>
                    <li><a href="#"
                            class="text-sm hover:text-white hover:translate-x-2 transition-all inline-block flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-[10px] text-orion-blue opacity-0 group-hover:opacity-100 transition-all"></i>
                            Career
                        </a></li>
                    <li><a href="#"
                            class="text-sm hover:text-white hover:translate-x-2 transition-all inline-block flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-[10px] text-orion-blue opacity-0 group-hover:opacity-100 transition-all"></i>
                            News & Events
                        </a></li>
                    <li><a href="#"
                            class="text-sm hover:text-white hover:translate-x-2 transition-all inline-block flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-[10px] text-orion-blue opacity-0 group-hover:opacity-100 transition-all"></i>
                            Global Presence
                        </a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-bold text-lg mb-6 relative inline-block">
                    Contact Us
                    <span class="absolute -bottom-2 left-0 w-8 h-1 bg-orion-blue"></span>
                </h3>
                <ul class="space-y-4">
                    <li class="flex items-start gap-4 text-sm">
                        <i class="fa-solid fa-location-dot text-orion-blue mt-1"></i>
                        <span>153-154, Tejgaon I/A,<br>Dhaka-1208, Bangladesh</span>
                    </li>
                    <li class="flex items-center gap-4 text-sm">
                        <i class="fa-solid fa-phone text-orion-blue"></i>
                        <span>+880 2 8870133</span>
                    </li>
                    <li class="flex items-center gap-4 text-sm">
                        <i class="fa-solid fa-envelope text-orion-blue"></i>
                        <a href="mailto:info@orionpharma.com"
                            class="hover:text-white transition-colors">info@orionpharma.com</a>
                    </li>
                </ul>
            </div>

        </div>

        <div class="pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-xs text-gray-500">
                &copy; {{ date('Y') }} <span class="text-gray-400 font-medium">ORION Pharma Limited</span>. All Rights
                Reserved.
            </p>
            <div class="flex gap-6 text-xs text-gray-500">
                <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-white transition-colors">Terms of Use</a>
                <a href="#" class="hover:text-white transition-colors">Sitemap</a>
            </div>
        </div>
    </div>
</footer>