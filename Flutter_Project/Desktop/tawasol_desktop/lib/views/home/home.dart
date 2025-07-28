import 'package:flutter/material.dart';
import 'package:tawasol_desktop/utils/constants/custom_scaffold.dart';
import 'package:tawasol_desktop/utils/theme/app_colors.dart';
import 'package:tawasol_desktop/utils/widgets/chat_title.dart';
import 'package:tawasol_desktop/utils/widgets/tab_button.dart';
import 'package:tawasol_desktop/utils/widgets/welcome.dart';
import 'package:tawasol_desktop/views/chat/chat.dart';

class HomePage extends StatefulWidget {
  const HomePage({Key? key}) : super(key: key);

  @override
  State<HomePage> createState() => _WhatsAppDesktopMockState();
}

class _WhatsAppDesktopMockState extends State<HomePage> {
  int? selectedChatIndex;

  // بيانات المحادثات
  final List<Map<String, dynamic>> chats = [
    {
      'name': 'ابوبكر فضل الشيباني',
      'message': 'هههههههههههه',
      'time': '05:14 pm',
      'avatar': 'assets/images/avatar1.png',
      'color': const Color(0xFFFFE082),
      'messages': [
        {'text': 'ههههههههههههههه', 'isMe': false, 'time': '05:14 pm'},
      ],
    },
    {
      'name': 'حسام خالد الزريقي',
      'message': "مرحبا كيف الحال 😂",
      'time': '07:38 am',
      'avatar': 'assets/images/avatar4.png',
      'color': const Color(0xFFB2DFDB),
      'messages': [
        {'text': "مرحبا كيف الحال 😂", 'isMe': false, 'time': '07:38 am'},
      ],
    },
    {
      'name': 'المهندس عبود',
      'message': 'رائع!',
      'time': '11:49 pm',
      'avatar': 'assets/images/avatar5.png',
      'color': const Color(0xFFB2DFDB),
      'unreadCount': '5+',
      'messages': [
        {'text': 'رائع!', 'isMe': false, 'time': '11:49 pm'},
      ],
    },
    // {
    //   'name': 'Marvin McKinney',
    //   'message': 'omg, this is amazing...',
    //   'time': '07:40 am',
    //   'avatar': 'assets/images/avatar4.png',
    //   'color': const Color(0xFFFFE082),
    //   'unreadCount': '1',
    //   'messages': [
    //     {'text': 'omg, this is amazing...', 'isMe': false, 'time': '07:40 am'},
    //   ],
    // },
    // {
    //   'name': 'Courtney Henry',
    //   'message': 'aww',
    //   'time': '08:20 pm',
    //   'avatar': 'assets/images/avatar5.png',
    //   'color': const Color(0xFFFFE082),
    //   'unreadCount': '1',
    //   'messages': [
    //     {'text': 'aww', 'isMe': false, 'time': '08:20 pm'},
    //   ],
    // },
    // {
    //   'name': 'Dianne Russell',
    //   'message': "I'll be there in 2 mins",
    //   'time': '02:15 pm',
    //   'avatar': 'assets/images/avatar6.png',
    //   'color': const Color(0xFFFFE082),
    //   'messages': [
    //     {'text': "I'll be there in 2 mins", 'isMe': false, 'time': '02:15 pm'},
    //   ],
    // },
  ];

  void sendMessageToChat(int chatIndex, String text) {
    final now = TimeOfDay.now();
    final timeString = now.format(context);
    setState(() {
      chats[chatIndex]['messages'].add({
        'text': text,
        'isMe': true,
        'time': timeString,
      });
    });
  }

  @override
  Widget build(BuildContext context) {
    return CustomScaffold(
      // backgroundColor: const Color(0xFFe7e3f6),
      child: Center(
        child: Container(
          // width: 1000,
          // height: 600,
          decoration: BoxDecoration(
            color: AppColors.white,
            borderRadius: BorderRadius.circular(24),
          ),
          child: Row(
            children: [
              // Sidebar
              Container(
                width: 360,
                decoration: const BoxDecoration(
                  boxShadow: [
                    BoxShadow(
                      blurRadius: 15,
                      color: AppColors.whiteSmoke,
                    ),
                  ],
                  color: AppColors.whiteSmoke,
                  borderRadius: BorderRadius.only(
                    // topLeft: Radius.circular(24),
                    bottomLeft: Radius.circular(24),
                  ),
                ),
                child: Column(
                  children: [
                    // Top bar
                    Padding(
                      padding: EdgeInsets.all(16.0),
                      child: Row(
                        children: [
                          Container(
                            padding: EdgeInsets.all(3),
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(50),
                              gradient: LinearGradient(
                                colors: [
                                  AppColors.primary,
                                  AppColors.secondary,
                                ],
                              ),
                            ),
                            child: Container(
                              padding: const EdgeInsets.all(3.0),
                              decoration: BoxDecoration(
                                color: AppColors.whiteSmoke,
                                borderRadius: BorderRadius.circular(50),
                              ),
                              child: CircleAvatar(
                                backgroundImage: AssetImage(
                                    'assets/images/avatar.png'), // ضع صورة رمزية هنا
                                radius: 16,
                              ),
                            ),
                          ),
                          Spacer(),
                          IconButton(
                            tooltip: 'تحديث',
                            onPressed: () {},
                            icon: Icon(
                              Icons.refresh,
                              color: AppColors.gray,
                            ),
                          ),
                          SizedBox(width: 3),
                          PopupMenuButton<String>(
                            onSelected: (value) {
                              ScaffoldMessenger.of(context).showSnackBar(
                                SnackBar(content: Text('تم اختيار: $value')),
                              );
                            },
                            itemBuilder: (BuildContext context) =>
                                <PopupMenuEntry<String>>[
                              PopupMenuItem<String>(
                                value: 'الاعدادات',
                                child: Text('الاعدادات'),
                              ),
                              PopupMenuItem<String>(
                                value: 'حول البرنامج',
                                child: Text('حول البرنامج'),
                              ),
                              PopupMenuItem<String>(
                                value: 'الخيار الثالث',
                                child: Text('الخيار الثالث'),
                              ),
                            ],
                            child: IconButton(
                              onPressed: null,
                              icon: Icon(
                                Icons.keyboard_arrow_down,
                                color: AppColors.gray,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    // Search bar
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 16.0),
                      child: TextField(
                        decoration: InputDecoration(
                          hintText: 'ابحث او إبدأ محادثة جديدة',
                          prefixIcon: const Icon(Icons.search),
                          filled: true,
                          fillColor: AppColors.white,
                          contentPadding:
                              const EdgeInsets.symmetric(vertical: 0),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(24),
                            borderSide: BorderSide.none,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),
                    // Tabs
                    const Padding(
                      padding: EdgeInsets.symmetric(horizontal: 16.0),
                      child: Row(
                        children: [
                          TabButton(text: 'المفضلة', selected: true),
                          TabButton(text: 'الأصدقاء'),
                          TabButton(text: 'المجموعات'),
                        ],
                      ),
                    ),
                    const SizedBox(height: 16),
                    // Chat List
                    Expanded(
                      child: ListView.builder(
                        itemCount: chats.length,
                        itemBuilder: (context, index) {
                          final chat = chats[index];
                          return InkWell(
                            onTap: () {
                              setState(() {
                                selectedChatIndex = index;
                              });
                            },
                            child: ChatTile(
                              name: chat['name'],
                              message: chat['message'],
                              time: chat['time'],
                              avatar: chat['avatar'],
                              color: chat['color'],
                              unreadCount: chat['unreadCount'],
                            ),
                          );
                        },
                      ),
                    ),
                    // Floating action button

                    Padding(
                      padding: const EdgeInsets.all(30.0),
                      child: InkWell(
                        onTap: () {
                          print('object');
                        },
                        child: Align(
                            alignment: Alignment.bottomLeft,
                            child: Container(
                              alignment: AlignmentDirectional.center,
                              // padding: const EdgeInsets.all(16.0),
                              height: 55,
                              width: 55,
                              decoration: BoxDecoration(
                                borderRadius: BorderRadiusDirectional.all(
                                    Radius.circular(15)),
                                gradient: LinearGradient(
                                  colors: [
                                    AppColors.primary,
                                    AppColors.secondary,
                                  ],
                                  begin: AlignmentDirectional.topStart,
                                  end: AlignmentDirectional.bottomEnd,
                                ),
                              ),
                              child: Icon(
                                Icons.chat,
                                color: AppColors.white,
                              ),
                            )),
                      ),
                    ),
                  ],
                ),
              ),
              // Main Area
              Expanded(
                child: selectedChatIndex == null
                    ? const WelcomePage()
                    : ChatPage(
                        chat: chats[selectedChatIndex!],
                        onSend: (msg) =>
                            sendMessageToChat(selectedChatIndex!, msg),
                      ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
