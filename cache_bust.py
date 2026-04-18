# import os
# import re

# project_dir = r"c:\xampp\htdocs\project"

# print("Updating CSS links to bypass cache...")
# for root, dirs, files in os.walk(project_dir):
#     for filename in files:
#         if filename.endswith('.php'):
#             filepath = os.path.join(root, filename)
#             with open(filepath, 'r', encoding='utf-8') as f:
#                 content = f.read()

#             # Add cache-busting version to style.css
#             new_content = re.sub(r'href="([^"]*css/style\.css)"', r'href="\1?v=' + str(int(os.path.getmtime(os.path.join(project_dir, 'css', 'style.css')))) + '"', content)
            
#             # If it already had a parameter like ?v=123, update it
#             new_content = re.sub(r'href="([^"]*css/style\.css)\?v=\d+"', r'href="\1?v=' + str(int(os.path.getmtime(os.path.join(project_dir, 'css', 'style.css')))) + '"', new_content)

#             if new_content != content:
#                 with open(filepath, 'w', encoding='utf-8') as f:
#                     f.write(new_content)
#                 print(f"Updated {filename}")

# print("Done!")
