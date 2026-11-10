@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('laporan.minggu9.download') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                ⬇️ Download PDF
            </a>
        </div>

        <iframe src="{{ asset('LAPORAN_FINAL_MINGGU_9.html') }}" 
                style="width:100%; height:1200px; border:1px solid #ddd; border-radius:5px;"
                frameborder="0"></iframe>
    </div>
</div>
@endsection
