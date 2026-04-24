{{-- Reusable confirm delete modal --}}
<div class="modal-backdrop" id="modal-confirm" onclick="closeModalOutside(event,'modal-confirm')">
  <div class="modal" style="max-width:380px;text-align:center">
    <div style="font-size:48px;margin-bottom:12px">🗑️</div>
    <h3 style="font-size:17px;font-weight:800;color:var(--g9);margin-bottom:8px" id="confirm-msg">Hapus item ini?</h3>
    <p style="font-size:13px;color:var(--g5);margin-bottom:24px">Tindakan ini tidak bisa dibatalkan.</p>
    <div style="display:flex;gap:10px;justify-content:center">
      <button class="btn btn-outline" onclick="toggleModal('modal-confirm')">Batal</button>
      <form id="confirm-form" method="POST">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
      </form>
    </div>
  </div>
</div>
