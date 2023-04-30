@include('templates.header')
<script src="{{asset('js/form.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/login.css') }}" />
<div class="w-100 h-100 loginBackground d-flex align-items-center justify-content-center">
  <div class="bg-gray-opacity h-100 w-100 align-items-center justify-content-center d-flex p-4">
    <div class="bg-white h-fit p-5 col-4" id="loginContainer">
      <h3 class="mb-3 w-100">Iyada System</h3>
      <form action="" method="" target="_blank" id="gg" class="dd">
        @csrf
        <div class="error mb-2 ml-2" style="color:red"></div>
        <input class="p-2 w-100 px-3" type="text" id="email" name="email" placeholder="Enter Email Address"><br><br>
        <input class="p-2 w-100 px-3" type="password" id="password" name="password" placeholder="Password"><br><br>
        <input type="checkbox" id="remember" name="remember" value="1">
        <label class="text-muted" for="remember"> Remember Me</label><br>
        <input class="w-100 btn btn-primary_1" id="login" type="submit" value="Login">
      </form>
      <hr>
      <button class="w-100 btn btn-danger mb-2"><i class="fa fa-brands fa-google"></i> Login With Google</button>
      <button class="w-100 btn btn-facebook text-white"><i class="fa fa-brands fa-facebook-f"></i> Login With Facebook</button>
      <hr>
      <div class="d-flex justify-content-center w-100 mb-1 text-primary"><a href="#">Forgot Password?</a></div>
      <div class="d-flex w-100 text-primary"><a href="#" class="mx-auto">Create an Account!</a></div>
    </div>
  </div>
</div>
</body>

</html>