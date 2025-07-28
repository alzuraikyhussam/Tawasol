import 'package:get/get.dart';
import 'package:tawasol_desktop/models/post_model.dart';
import 'package:tawasol_desktop/services/api/api_services.dart';
import 'package:tawasol_desktop/utils/constants/api_endpoints.dart';

class PostController extends GetxController {
  final ApiServices _apiService = ApiServices();

  Future<List<PostModel>> fetchPosts() async {
    final response = await _apiService.get(ApiEndpoints.login);

    if (response != null && response.statusCode == 200) {
      final List data = response.data;
      return data.map((item) => PostModel.fromJson(item)).toList();
    } else {
      throw Exception('Failed to load posts');
    }
  }
}




// import 'package:flutter/material.dart';
// import 'post_model.dart';
// import 'post_controller.dart';

// class PostsPage extends StatelessWidget {
//   final PostController controller = PostController();

//   @override
//   Widget build(BuildContext context) {
//     return Scaffold(
//       appBar: AppBar(title: Text('Posts')),
//       body: FutureBuilder<List<PostModel>>(
//         future: controller.fetchPosts(),
//         builder: (context, snapshot) {
//           if (snapshot.connectionState == ConnectionState.waiting) {
//             return const Center(child: CircularProgressIndicator());
//           } else if (snapshot.hasError) {
//             return Center(child: Text('خطأ: ${snapshot.error}'));
//           } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
//             return const Center(child: Text('لا توجد بيانات'));
//           } else {
//             final posts = snapshot.data!;
//             return ListView.builder(
//               itemCount: posts.length,
//               itemBuilder: (context, index) {
//                 final post = posts[index];
//                 return ListTile(
//                   title: Text(post.title),
//                   subtitle: Text(post.body),
//                 );
//               },
//             );
//           }
//         },
//       ),
//     );
//   }
// }

