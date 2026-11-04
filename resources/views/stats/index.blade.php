<x-app-layout>
  <x-slot name="header"><h2 class="text-xl font-semibold">Thống kê</h2></x-slot>
  <div class="max-w-4xl mx-auto py-6 px-4 grid md:grid-cols-2 gap-6">
    <div class="bg-white border rounded p-4 md:col-span-2">
      <h3 class="font-semibold mb-2">Câu hỏi theo môn</h3>
      @php $max = max(1, $bySubject->max('questions_count')); @endphp
      @foreach($bySubject as $s)
        <div class="mb-2">
          <div class="text-sm">{{ $s->code }} - {{ $s->name }}: {{ $s->questions_count }} câu ({{ $s->answered_count }} đã có đáp án)</div>
          <div class="h-2 bg-gray-100 rounded"><div class="h-2 bg-blue-600 rounded" style="width: {{ $s->questions_count / $max * 100 }}%"></div></div>
        </div>
      @endforeach
    </div>
    <div class="bg-white border rounded p-4">
      <h3 class="font-semibold mb-2">Uy tín cao nhất</h3>
      @foreach($topRep as $u)<div class="text-sm flex justify-between"><span>{{ $u->name }}</span><span>{{ $u->reputation }}</span></div>@endforeach
    </div>
    <div class="bg-white border rounded p-4">
      <h3 class="font-semibold mb-2">Trả lời được chấp nhận trong tháng</h3>
      @foreach($topMonth as $u)<div class="text-sm flex justify-between"><span>{{ $u->name }}</span><span>{{ $u->accepted_month }}</span></div>@endforeach
    </div>
    <div class="bg-white border rounded p-4 md:col-span-2">
      <h3 class="font-semibold mb-2">Người đóng góp nổi bật</h3>
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead><tr class="border-b"><th class="py-2">Người dùng</th><th>Câu hỏi</th><th>Trả lời</th><th>Được chấp nhận</th><th>Uy tín</th></tr></thead>
          <tbody>
            @foreach($top as $u)
              <tr class="border-b last:border-0">
                <td class="py-2"><a href="{{ route('users.show', $u->id) }}" class="text-blue-700 hover:underline">{{ $u->name }}</a></td>
                <td>{{ $u->questions }}</td><td>{{ $u->answers }}</td><td>{{ $u->accepted }}</td><td>{{ $u->reputation }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</x-app-layout>
