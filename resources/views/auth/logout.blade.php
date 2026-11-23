<form class="footer" method="post" action="{{ route('logout') }}">
    @csrf
    <button class="secondary" type="submit">Cerrar sesión</button>
</form>
