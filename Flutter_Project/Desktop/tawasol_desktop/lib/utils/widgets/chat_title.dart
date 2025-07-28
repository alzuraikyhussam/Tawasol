import 'package:flutter/material.dart';
import 'package:tawasol_desktop/utils/theme/app_colors.dart';

class ChatTile extends StatelessWidget {
  final String name;
  final String message;
  final String time;
  final String avatar;
  final Color color;
  final bool isRead;
  final String? unreadCount;

  const ChatTile({
    required this.name,
    required this.message,
    required this.time,
    required this.avatar,
    required this.color,
    this.isRead = false,
    this.unreadCount,
    super.key,
  });

  @override
  Widget build(BuildContext context) {
    return ListTile(
      leading: CircleAvatar(
        backgroundColor: color,
        backgroundImage: AssetImage(avatar),
      ),
      title: Text(name, style: const TextStyle(fontWeight: FontWeight.bold)),
      subtitle: Text(message, maxLines: 1, overflow: TextOverflow.ellipsis),
      trailing: Column(
        crossAxisAlignment: CrossAxisAlignment.end,
        children: [
          Text(time,
              style: const TextStyle(fontSize: 12, color: AppColors.gray)),
          if (unreadCount != null)
            Container(
              margin: const EdgeInsets.only(top: 4),
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [
                    AppColors.primary,
                    AppColors.secondary,
                  ],
                  begin: AlignmentDirectional.topStart,
                  end: AlignmentDirectional.bottomEnd,
                ),
                // color: AppColors.secondary,
                borderRadius: BorderRadius.circular(12),
              ),
              child: Text(
                unreadCount!,
                style: const TextStyle(color: AppColors.white, fontSize: 12),
              ),
            ),
        ],
      ),
    );
  }
}
