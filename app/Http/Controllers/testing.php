    // BACKEND LOGIN - Versi Sendiri
    public function login(Request $request)
    {
    $loginData = $request->all();
    $validator = Validator::make($loginData, [
    'EMAIL' => 'required|email',
    'PASSWORD' => 'required'
    ]);

    if (is_null($request->EMAIL) || is_null($request->PASSWORD)) {
    return response(['message' => 'Please provide both email and password'], 400);
    }

    $user = null;

    // Check if the user is a member
    $member = member::where('EMAIL', '=', $loginData['EMAIL'])->first();
    if ($member && Hash::check($loginData['PASSWORD'], $member['PASSWORD'])) {
    $user = $member;
    $token = bcrypt(uniqid());

    return response([
    'message' => 'Successfully logged in as Member',
    'data' => $user,
    'token_type' => 'Bearer',
    'access_token' => $token
    ]);
    }

    // Check if the user is a instructor
    if (!$user) {
    $instructor = instructor::where('EMAIL', '=', $loginData['EMAIL'])->first();
    if ($instructor && Hash::check($loginData['PASSWORD'], $instructor['PASSWORD'])) {
    $user = $instructor;
    $token = bcrypt(uniqid());

    return response([
    'message' => 'Successfully logged in as Instructor',
    'data' => $user,
    'token_type' => 'Bearer',
    'access_token' => $token
    ]);
    }
    }

    // Check if the user is a pegawai
    if (!$user) {
    $pegawai = pegawai::where('EMAIL', '=', $loginData['PASSWORD'])->first();
    if ($pegawai && Hash::check($loginData['PASSWORD'], $pegawai['PASSWORD'])) {
    $user = $pegawai;
    $token = bcrypt(uniqid());

    return response([
    'message' => 'Successfully logged in as Pegawai',
    'data' => $user,
    'token_type' => 'Bearer',
    'access_token' => $token
    ]);
    }
    }

    // Return error if no match
    if (!$user) {
    return response([
    'message' => 'Invalid email or password',
    ], 404);
    }

    // Return a success message with user data and token
    // return response([
    // 'message' => 'Successfully logged in',
    // 'data' => $user,
    // 'token_type' => 'Bearer',
    // 'access_token' => $token
    // ]);

    if ($validator->fails()) {
    return response(['message' => $validator->errors()], 422);
    }
    }












    // BACKEND LOGIN - Versi kak Edward
    public function login(Request $request)
    {
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'username' => 'required',
            'password' => 'required'
        ]);

        if(is_null($request->username) || is_null($request->password)){
            return response(['message' => 'Inputan tidak boleh kosong'], 400);
        }
        $pegawai = null;

        //get token with random string//
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        if(Pegawai::where('username','=',$loginData['username'])->first())
        {
            $loginPegawai = Pegawai::where('username','=',$loginData['username'])->first();

            if(Hash::check($loginData['password'], $loginPegawai['password'])){
                $pegawai = Pegawai::where('username',$loginData['username'])->first();
            }
            else{
                return response([
                    'message' => 'pegawai username atau password salah',
                    'data' => $pegawai
                ], 400);
            }
            $token = bcrypt($randomString);
            return response([
                'message' => 'berhasil login sebagai pegawai',
                'data' => $pegawai,
                'token' => $token
            ]);
        }else{
            $loginMember = Member::where('username','=',$loginData['username'])->first();

            if(Hash::check($loginData['password'], $loginMember['password'])){
                $Member = Member::where('username',$loginData['username'])->first();
            }
            else{
                return response([
                    'message' => 'username atau password salah',
                    // 'data' => $Member
                ], 400);
            }
            $token = bcrypt($randomString);
            return response([
                'message' => 'berhasil login sebagai Member',
                'data' => $Member,
                'token' => $token
            ]);
        }

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);
    }







        // FRONTEND LOGIN - Versi kak Edward
    submit() {
      let url = this.$api + "/login";
      this.$http
        .post(url, {
          username: this.username,
          password: this.password,
        })
        .then((response) => {
          if (response.data.data.id_pegawai != null) {
            localStorage.setItem("token", response.data.token);
            localStorage.setItem("id_pegawai", response.data.data.id_pegawai);
            localStorage.setItem(
              "nama_pegawai",
              response.data.data.nama_pegawai
            );
            localStorage.setItem("role", response.data.data.role);

            this.$router.push("/dashboard");
            this.error_message = response.data.message;
            this.color = "blue";
            this.snackbar = true;
            this.clear();
            this.load = false;
          } else {
            localStorage.setItem("id_member", response.data.data.id_member);
            this.$router.push("/dashboard");
          }
        })
        .catch((error) => {
          this.error_message = error.response.data.message;
          this.color = "red";
          this.snackbar = true;
          localStorage.removeItem("token");
          this.load = false;
        });
    },


        // InstructorAbsentController