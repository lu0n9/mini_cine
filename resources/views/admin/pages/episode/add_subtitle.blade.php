<!-- ===== UPLOAD SUBTITLE ===== -->      
@extends('admin.layouts.master')
@section('content')
<section id="upload-subtitle" class="page">
        <div class="page-head"><div><h3>Upload subtitle</h3><p>Tải phụ đề lên episode theo ngôn ngữ và định dạng.</p></div><a class="btn ghost" href="#subtitles">← Quay lại subtitles</a></div>
        <div class="panel"><div class="panel-body"><div class="form-grid">
          <div class="field"><label>Phim / Episode *</label><select><option>Crimson Vale · Tập 12</option><option>Crimson Vale · Tập 11</option><option>Iron Verdict · Tập 08</option></select></div>
          <div class="field"><label>Ngôn ngữ *</label><select><option>Tiếng Việt</option><option>English</option><option>한국어</option><option>日本語</option></select></div>
          <div class="field"><label>Label hiển thị</label><input value="Vietsub" /></div>
          <div class="field"><label>Định dạng</label><select><option>VTT (.vtt)</option><option>SRT (.srt)</option></select></div>
          <label class="upload full"><input type="file" accept=".srt,.vtt" /><b>Chọn file phụ đề</b><br />Kéo thả file .srt hoặc .vtt vào đây<br /><small>Tối đa 10MB</small></label>
          <div class="setting-row field full"><div class="st-txt"><strong>Đặt làm phụ đề mặc định</strong><small>Tự động bật cho người xem khi phát episode.</small></div><label class="toggle"><input type="checkbox" checked /><span class="track"></span></label></div>
          <div class="form-actions"><a class="btn ghost" href="#subtitles">Hủy</a><button class="btn">Upload subtitle</button></div>
        </div></div></div>
      </section>
      @endsection