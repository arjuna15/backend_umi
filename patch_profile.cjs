const fs = require('fs');
const path = require('path');

// 1. Update the migration file
const migrationsDir = './database/migrations';
const files = fs.readdirSync(migrationsDir);
const migrationFile = files.find(f => f.includes('add_profile_columns_to_users_table'));
if (migrationFile) {
    const filePath = path.join(migrationsDir, migrationFile);
    let content = fs.readFileSync(filePath, 'utf8');
    content = content.replace('public function up(): void\n    {', `public function up(): void\n    {\n        Schema::table('users', function (Blueprint $table) {\n            $table->string('phone')->nullable();\n            $table->text('address')->nullable();\n            $table->text('bio')->nullable();\n            $table->string('avatar_url')->nullable();\n        });`);
    content = content.replace('public function down(): void\n    {', `public function down(): void\n    {\n        Schema::table('users', function (Blueprint $table) {\n            $table->dropColumn(['phone', 'address', 'bio', 'avatar_url']);\n        });`);
    fs.writeFileSync(filePath, content);
}

// 2. Update routes/api.php
const routesPath = './routes/api.php';
let routesContent = fs.readFileSync(routesPath, 'utf8');
if (!routesContent.includes('/profile/update')) {
    const siakadGroupAnchor = "Route::post('/siakad/profile/password', [\\App\\Http\\Controllers\\SiakadController::class, 'updatePassword']);";
    const newRoutes = `Route::post('/siakad/profile/password', [\\App\\Http\\Controllers\\SiakadController::class, 'updatePassword']);\n    Route::post('/siakad/profile/update', [\\App\\Http\\Controllers\\SiakadController::class, 'updateProfile']);\n    Route::post('/siakad/profile/upload-avatar', [\\App\\Http\\Controllers\\SiakadController::class, 'uploadAvatar']);`;
    routesContent = routesContent.replace(siakadGroupAnchor, newRoutes);
    fs.writeFileSync(routesPath, routesContent);
}

// 3. Update SiakadController.php
const controllerPath = './app/Http/Controllers/SiakadController.php';
let controllerContent = fs.readFileSync(controllerPath, 'utf8');
if (!controllerContent.includes('public function updateProfile')) {
    const passwordEndAnchor = `public function updatePassword(Request $request)\n    {\n        $request->validate([\n            'current_password' => 'required',\n            'new_password' => 'required|min:6'\n        ]);\n\n        $user = auth()->user();\n\n        if (!\\Hash::check($request->current_password, $user->password)) {\n            return response()->json(['message' => 'Password saat ini salah'], 400);\n        }\n\n        $user->update([\n            'password' => \\Hash::make($request->new_password)\n        ]);\n\n        return response()->json(['message' => 'Password berhasil diubah']);\n    }`;
    
    const newMethods = `\n\n    public function updateProfile(Request $request)\n    {\n        $user = auth()->user();\n        $request->validate([\n            'email' => 'nullable|email|unique:users,email,'.$user->id,\n            'phone' => 'nullable|string',\n            'address' => 'nullable|string',\n            'bio' => 'nullable|string',\n        ]);\n\n        $user->update([\n            'email' => $request->email ?? $user->email,\n            'phone' => $request->phone,\n            'address' => $request->address,\n            'bio' => $request->bio,\n        ]);\n\n        return response()->json(['message' => 'Profil berhasil diperbarui', 'user' => $user]);\n    }\n\n    public function uploadAvatar(Request $request)\n    {\n        $request->validate([\n            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'\n        ]);\n\n        $user = auth()->user();\n        \n        if ($request->hasFile('avatar')) {\n            $file = $request->file('avatar');\n            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();\n            $file->move(public_path('uploads/avatars'), $filename);\n            \n            $user->update([\n                'avatar_url' => url('/uploads/avatars/' . $filename)\n            ]);\n            \n            return response()->json(['message' => 'Avatar berhasil diunggah', 'avatar_url' => $user->avatar_url]);\n        }\n\n        return response()->json(['message' => 'Gagal mengunggah avatar'], 400);\n    }`;
    
    controllerContent = controllerContent.replace(passwordEndAnchor, passwordEndAnchor + newMethods);
    fs.writeFileSync(controllerPath, controllerContent);
}

// 4. Update User model to allow mass assignment for new columns
const modelPath = './app/Models/User.php';
let modelContent = fs.readFileSync(modelPath, 'utf8');
if (!modelContent.includes("'phone', 'address', 'bio', 'avatar_url'")) {
    modelContent = modelContent.replace("'email',", "'email',\n        'phone',\n        'address',\n        'bio',\n        'avatar_url',");
    fs.writeFileSync(modelPath, modelContent);
}

console.log('Profile backend patched successfully!');
