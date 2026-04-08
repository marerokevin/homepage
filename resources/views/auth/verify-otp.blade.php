<form method="POST" action="{{ route('otp.verify') }}">
    @csrf
    <label>Email</label>
    <input type="email" name="email" required>

    <label>OTP</label>
    <input type="text" name="otp" required maxlength="6">

    <button type="submit">Verify OTP</button>
</form>
