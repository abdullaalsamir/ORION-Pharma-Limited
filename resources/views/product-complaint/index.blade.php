@extends('layouts.app')

@section('content')
    <div class="py-16 bg-slate-50 min-h-screen">
        <div class="grid grid-cols-1 lg:grid-cols-10 gap-12 max-w-[1400px] mx-auto px-4">

            <div class="lg:col-span-6">
                <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 p-8 md:p-12">
                    <div class="flex justify-between items-center mb-10 border-b border-slate-100 pb-6">
                        <h2 class="text-2xl font-black text-slate-800">Product Complaint Form</h2>
                        <div class="text-right">
                            <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-widest">Complaint
                                Raising Date</span>
                            <span class="text-orion-blue font-black text-lg">{{ $raisingDate }}</span>
                        </div>
                    </div>

                    @if(session('success'))
                        <div
                            class="mb-8 p-4 bg-green-50 text-green-700 rounded-2xl border border-green-100 font-bold text-center">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('complaint.submit') }}" method="POST" class="space-y-10">
                        @csrf

                        <div>
                            <h3
                                class="text-orion-blue font-black uppercase text-sm tracking-widest mb-6 flex items-center gap-3">
                                <span class="w-8 h-px bg-orion-blue/20"></span> SECTION – A: Product Identification
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Product Name</label>
                                    <input type="text" name="product_name" required
                                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-orion-blue transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Batch Number</label>
                                    <input type="text" name="batch_number" required
                                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-orion-blue transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Manufacturing
                                        Date</label>
                                    <input type="date" name="mfg_date" required
                                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-orion-blue transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Expiry Date</label>
                                    <input type="date" name="exp_date" required
                                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-orion-blue transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Strength</label>
                                    <input type="text" name="strength"
                                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-orion-blue transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Dosage Form</label>
                                    <input type="text" name="dosage_form"
                                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-orion-blue transition-all">
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3
                                class="text-orion-blue font-black uppercase text-sm tracking-widest mb-6 flex items-center gap-3">
                                <span class="w-8 h-px bg-orion-blue/20"></span> SECTION – B: Nature of Complaint
                            </h3>
                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Type of Complaint</label>
                                    <input type="text" name="complaint_type"
                                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-orion-blue transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Description of
                                        Complaint</label>
                                    <textarea name="complaint_description" rows="4"
                                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-orion-blue transition-all resize-none"></textarea>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3
                                class="text-orion-blue font-black uppercase text-sm tracking-widest mb-6 flex items-center gap-3">
                                <span class="w-8 h-px bg-orion-blue/20"></span> SECTION – C: Complainant Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Name of
                                        Complainant</label>
                                    <input type="text" name="complainant_name" required
                                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-orion-blue transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Contact Number</label>
                                    <input type="text" name="contact_number" required
                                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-orion-blue transition-all">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Address</label>
                                <textarea name="address" rows="3"
                                    class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-orion-blue transition-all resize-none"></textarea>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full py-5 bg-orion-blue text-white font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                            Submit Complaint
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-4">
                <div class="sticky top-[110px] space-y-6">
                    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100">
                        <p class="text-slate-500 font-medium mb-8 leading-relaxed">For any inquiry or to report Product
                            Complaint, please contact us:</p>

                        <div class="space-y-8">
                            <div class="flex items-start gap-6 group">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-orion-blue shrink-0 group-hover:bg-orion-blue group-hover:text-white transition-all">
                                    <i class="fa-solid fa-phone-volume text-xl"></i>
                                </div>
                                <div>
                                    <div
                                        class="bg-orion-blue text-white px-4 py-1 rounded-lg font-black text-lg inline-block mb-2 shadow-md italic tracking-wider">
                                        +88 01897 65 31 31
                                    </div>
                                    <p class="text-sm font-bold text-slate-700">10:00 - 18:00</p>
                                    <p class="text-xs text-slate-400 font-medium italic">Except Friday and Government
                                        Holidays</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-6 group border-t border-slate-50 pt-8">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-orion-blue shrink-0 group-hover:bg-orion-blue group-hover:text-white transition-all">
                                    <i class="fa-solid fa-envelope-open-text text-xl"></i>
                                </div>
                                <div>
                                    <div
                                        class="bg-orion-blue text-white px-4 py-1 rounded-lg font-black text-lg inline-block shadow-md italic tracking-wider uppercase">
                                        pc@orion-group.net
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-8 py-6 bg-orion-blue/5 rounded-3xl border border-orion-blue/10">
                        <p class="text-[10px] font-black text-orion-blue uppercase tracking-[0.3em] text-center">Quality
                            Assurance Division</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection