import 'dart:async';

import 'package:emoji_picker_flutter/emoji_picker_flutter.dart';
import 'package:flutter/material.dart';
import 'package:just_audio/just_audio.dart';
import 'package:tawasol_desktop/utils/theme/app_colors.dart';
import 'package:tawasol_desktop/utils/widgets/profile_dialog.dart';

class ChatPage extends StatefulWidget {
  final Map<String, dynamic> chat;
  final void Function(String) onSend;
  const ChatPage({super.key, required this.chat, required this.onSend});

  @override
  State<ChatPage> createState() => _ChatPageState();
}

class _ChatPageState extends State<ChatPage> {
  final TextEditingController _controller = TextEditingController();
  int? editingMsgIndex;
  bool isRecording = false;
  bool isPaused = false;
  int recordSeconds = 0;
  Timer? _timer;
  int? playingIndex;
  bool isPlaying = false;
  bool showEmojiPicker = false;

  final AudioPlayer _audioPlayer = AudioPlayer();

  @override
  void initState() {
    super.initState();
    _timer = Timer.periodic(Duration(seconds: 1), (_) {
      if (isRecording) {
        setState(() {
          recordSeconds++;
        });
      }
    });
  }

  @override
  void dispose() {
    _controller.dispose();
    _timer?.cancel();
    _audioPlayer.dispose();
    super.dispose();
  }

  void startRecording() {
    setState(() {
      isRecording = true;
      isPaused = false;
      recordSeconds = 0;
    });
    _timer = Timer.periodic(Duration(seconds: 1), (_) {
      if (isRecording && !isPaused) {
        setState(() {
          recordSeconds++;
        });
      }
    });
  }

  void pauseRecording() {
    setState(() {
      isPaused = true;
    });
  }

  void resumeRecording() {
    setState(() {
      isPaused = false;
    });
  }

  void stopRecording({bool send = false}) {
    _timer?.cancel();
    if (send && recordSeconds > 0) {
      final messages = widget.chat['messages'] as List<dynamic>;
      setState(() {
        messages.add({
          'isMe': true,
          'isVoice': true,
          'voiceDuration': recordSeconds,
          'time': TimeOfDay.now().format(context),
        });
      });
    }
    setState(() {
      isRecording = false;
      isPaused = false;
      recordSeconds = 0;
    });
  }

  void playVoice(int index, int duration) {
    setState(() {
      playingIndex = index;
      isPlaying = true;
    });
    Future.delayed(Duration(seconds: duration), () {
      if (mounted && playingIndex == index) {
        setState(() {
          isPlaying = false;
          playingIndex = null;
        });
      }
    });
  }

  void stopVoice() {
    setState(() {
      isPlaying = false;
      playingIndex = null;
    });
  }

  String formatSeconds(int s) {
    final m = (s ~/ 60).toString().padLeft(2, '0');
    final sec = (s % 60).toString().padLeft(2, '0');
    return '$m:$sec';
  }

  void startEdit(int index, String text) {
    setState(() {
      editingMsgIndex = index;
      _controller.text = text;
    });
  }

  void saveEdit() {
    if (editingMsgIndex != null) {
      final messages = widget.chat['messages'] as List<dynamic>;
      setState(() {
        messages[editingMsgIndex!]['text'] = _controller.text.trim();
        editingMsgIndex = null;
        _controller.clear();
      });
    }
  }

  void deleteMsg(int index) {
    final messages = widget.chat['messages'] as List<dynamic>;
    setState(() {
      messages.removeAt(index);
      if (editingMsgIndex == index) {
        editingMsgIndex = null;
        _controller.clear();
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final messages = widget.chat['messages'] as List<dynamic>;
    return Column(
      children: [
        // شريط أعلى المحادثة (اسم وصورة)
        Container(
          padding: EdgeInsets.all(16),
          child: Row(
            children: [
              GestureDetector(
                onTap: () {
                  showDialog(
                    context: context,
                    builder: (ctx) => ProfileDialog(
                      name: widget.chat['name'],
                      avatar: widget.chat['avatar'],
                      about: widget.chat['about'] ??
                          '"إنها رحلة إنسان يحمل قلبه في فمه.."',
                    ),
                  );
                },
                child: Container(
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
                      backgroundImage: AssetImage(widget.chat['avatar']),
                      radius: 18,
                      // radius: 28,
                    ),
                  ),
                ),
              ),
              SizedBox(width: 12),
              Expanded(
                child: GestureDetector(
                  onTap: () {
                    showDialog(
                      context: context,
                      builder: (ctx) => ProfileDialog(
                        name: widget.chat['name'],
                        avatar: widget.chat['avatar'],
                        about: widget.chat['about'] ??
                            '"إنها رحلة إنسان يحمل قلبه في فمه.."',
                      ),
                    );
                  },
                  child: Text(
                    widget.chat['name'],
                    style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
              ),
              Spacer(),
              Icon(Icons.call, color: Colors.grey[600]),
              SizedBox(width: 8),
              Icon(Icons.videocam, color: Colors.grey[600]),
              SizedBox(width: 8),
              Icon(Icons.search, color: Colors.grey[600]),
              SizedBox(width: 8),
              Icon(Icons.more_vert, color: Colors.grey[600]),
            ],
          ),
        ),
        // مثال: رسالة وصور
        Expanded(
          child: ListView.builder(
            reverse: false,
            itemCount: messages.length,
            itemBuilder: (context, index) {
              final msg = messages[index];
              final isMe = msg['isMe'] == true;
              if (msg['isVoice'] == true) {
                final duration = msg['voiceDuration'] ?? 0;
                final isMsgPlaying = isPlaying && playingIndex == index;
                return Align(
                  alignment:
                      isMe ? Alignment.centerRight : Alignment.centerLeft,
                  child: Container(
                    margin: EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                    padding: EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: isMe ? AppColors.whiteSmoke : AppColors.lightGray,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        IconButton(
                          icon: Icon(
                              isMsgPlaying ? Icons.stop : Icons.play_arrow,
                              color: AppColors.secondary),
                          onPressed: () {
                            if (isMsgPlaying) {
                              stopVoice();
                            } else {
                              playVoice(index, duration);
                            }
                          },
                        ),
                        SizedBox(width: 8),
                        Text(formatSeconds(duration),
                            style: TextStyle(color: AppColors.gray)),
                        SizedBox(width: 8),
                        Text(msg['time'] ?? '',
                            style: TextStyle(
                                fontSize: 10, color: AppColors.lightGray)),
                      ],
                    ),
                  ),
                );
              }
              return GestureDetector(
                onLongPress: isMe
                    ? () async {
                        final result = await showDialog<String>(
                          context: context,
                          builder: (ctx) => AlertDialog(
                            shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(16)),
                            contentPadding: EdgeInsets.zero,
                            content: Column(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                ListTile(
                                  leading: Icon(Icons.edit),
                                  title: Text('تعديل'),
                                  onTap: () => Navigator.pop(ctx, 'edit'),
                                ),
                                ListTile(
                                  leading: Icon(Icons.delete),
                                  title: Text('حذف'),
                                  onTap: () => Navigator.pop(ctx, 'delete'),
                                ),
                              ],
                            ),
                          ),
                        );
                        if (result == 'edit') {
                          startEdit(index, msg['text'] ?? '');
                        } else if (result == 'delete') {
                          deleteMsg(index);
                        }
                      }
                    : null,
                child: Align(
                  alignment:
                      isMe ? Alignment.centerRight : Alignment.centerLeft,
                  child: Container(
                    margin: EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                    padding: EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: isMe
                          ? AppColors.secondary.withOpacity(0.3)
                          : AppColors.whiteSmoke,
                      // borderRadius: BorderRadius.circular(12),
                      borderRadius: BorderRadiusDirectional.only(
                        bottomEnd: Radius.circular(12),
                        bottomStart: Radius.circular(12),
                        topEnd: isMe ? Radius.circular(12) : Radius.circular(1),
                        topStart:
                            isMe ? Radius.circular(1) : Radius.circular(12),
                      ),
                    ),
                    child: Column(
                      crossAxisAlignment: isMe
                          ? CrossAxisAlignment.end
                          : CrossAxisAlignment.start,
                      children: [
                        Text(
                          msg['text'] ?? '',
                          style: TextStyle(
                              fontSize: 14,
                              color: isMe ? AppColors.black : AppColors.black),
                        ),
                        SizedBox(height: 10),
                        Text(
                          msg['time'] ?? '',
                          style: TextStyle(fontSize: 10, color: AppColors.gray),
                        ),
                      ],
                    ),
                  ),
                ),
              );
            },
          ),
        ),
        // شريط الكتابة أسفل المحادثة
        Container(
          padding: EdgeInsets.all(12),
          child: isRecording
              ? Row(
                  children: [
                    Icon(Icons.mic, color: AppColors.secondary),
                    SizedBox(width: 8),
                    Text(
                      formatSeconds(recordSeconds),
                      style: TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 18,
                          color: AppColors.secondary),
                    ),
                    SizedBox(width: 8),
                    IconButton(
                      icon: Icon(isPaused ? Icons.play_arrow : Icons.pause,
                          color: AppColors.secondary),
                      onPressed: () {
                        if (isPaused) {
                          resumeRecording();
                        } else {
                          pauseRecording();
                        }
                      },
                    ),
                    Spacer(),
                    IconButton(
                      icon: Icon(Icons.close, color: AppColors.gray),
                      onPressed: () => stopRecording(send: false),
                    ),
                    Container(
                      padding: EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(50),
                        gradient: LinearGradient(
                          colors: [
                            AppColors.primary,
                            AppColors.secondary,
                          ],
                          begin: AlignmentDirectional.topStart,
                          end: AlignmentDirectional.bottomEnd,
                        ),
                      ),
                      child: IconButton(
                        icon: Icon(Icons.send, color: AppColors.white),
                        onPressed: () => stopRecording(send: true),
                      ),
                    ),
                  ],
                )
              : Column(
                  children: [
                    Row(
                      children: [
                        IconButton(
                          icon: Icon(Icons.emoji_emotions_outlined),
                          onPressed: () {
                            setState(() {
                              showEmojiPicker = !showEmojiPicker;
                            });
                          },
                        ),
                        SizedBox(width: 8),
                        Expanded(
                          child: TextField(
                            controller: _controller,
                            decoration: InputDecoration(
                              hintText: editingMsgIndex == null
                                  ? "اكتب شيئاً..."
                                  : "تعديل الرسالة...",
                              border: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(15),
                                borderSide: BorderSide.none,
                              ),
                              filled: true,
                              fillColor: AppColors.whiteSmoke,
                            ),
                            onSubmitted: (val) {
                              if (val.trim().isNotEmpty) {
                                if (editingMsgIndex == null) {
                                  widget.onSend(val.trim());
                                } else {
                                  saveEdit();
                                }
                                _controller.clear();
                                setState(() {});
                              }
                            },
                          ),
                        ),
                        SizedBox(width: 8),
                        editingMsgIndex == null
                            ? Container(
                                padding: EdgeInsets.all(8),
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(50),
                                  gradient: LinearGradient(
                                    colors: [
                                      AppColors.primary,
                                      AppColors.secondary,
                                    ],
                                    begin: AlignmentDirectional.topStart,
                                    end: AlignmentDirectional.bottomEnd,
                                  ),
                                ),
                                child: IconButton(
                                  icon: Icon(
                                    Icons.send,
                                    color: AppColors.white,
                                  ),
                                  onPressed: () {
                                    final val = _controller.text.trim();
                                    if (val.isNotEmpty) {
                                      widget.onSend(val);
                                      _controller.clear();
                                      setState(() {});
                                    }
                                  },
                                ),
                              )
                            : IconButton(
                                icon: Icon(Icons.check),
                                onPressed: () {
                                  if (_controller.text.trim().isNotEmpty) {
                                    saveEdit();
                                    setState(() {});
                                  }
                                },
                              ),
                        IconButton(
                          icon: Icon(Icons.mic, color: AppColors.secondary),
                          onPressed: startRecording,
                        ),
                      ],
                    ),
                    if (showEmojiPicker)
                      SizedBox(
                        height: 300,
                        child: EmojiPicker(
                          onEmojiSelected: (category, emoji) {
                            _controller
                              ..text += emoji.emoji
                              ..selection = TextSelection.fromPosition(
                                TextPosition(offset: _controller.text.length),
                              );
                          },
                          onBackspacePressed: () {
                            final text = _controller.text;
                            if (text.isNotEmpty) {
                              _controller.text =
                                  text.characters.skipLast(1).toString();
                              _controller.selection =
                                  TextSelection.fromPosition(
                                TextPosition(offset: _controller.text.length),
                              );
                            }
                          },
                          textEditingController: _controller,
                          config: Config(
                              //   columns: 7,
                              //   emojiSizeMax: 32,
                              //   verticalSpacing: 0,
                              //   horizontalSpacing: 0,
                              //   gridPadding: EdgeInsets.zero,
                              //   initCategory: Category.RECENT,
                              //   bgColor: const Color(0xFFF2F2F2),
                              //   indicatorColor: Colors.blue,
                              //   iconColor: Colors.grey,
                              //   iconColorSelected: Colors.blue,
                              //   backspaceColor: Colors.blue,
                              //   skinToneDialogBgColor: Colors.white,
                              //   skinToneIndicatorColor: Colors.grey,
                              //   enableSkinTones: true,
                              //  // showRecentsTab: true,
                              //   recentsLimit: 28,
                              //   noRecents: const Text(
                              //     'No Recents',
                              //     style: TextStyle(fontSize: 20, color: Colors.black26),
                              //     textAlign: TextAlign.center,
                              //   ),
                              //   tabIndicatorAnimDuration: kTabScrollDuration,
                              //   categoryIcons: const CategoryIcons(),
                              //   buttonMode: ButtonMode.MATERIAL,
                              ),
                        ),
                      ),
                  ],
                ),
        ),
      ],
    );
  }
}
