#!/usr/bin/python

import sys
import argparse
import json
import re
import time

if sys.version_info[0]==3:
    twoarg = lambda s: map(lambda p:p.strip(), s.strip().split(" ", maxsplit=1))
    replace = lambda s, o, n: s.replace(o, n)
else:
    import string
    twoarg = lambda s: map(lambda p:p.strip(), string.split(s.strip(), " ", maxsplit=1))
    replace = lambda s, o, n: string.replace(s, o, n)

class ParseException(Exception):
    def __init__(self, message):
        super(ParseException, self).__init__("%s at line %d in %s." % (message, index, path))

def parse_user(_path):
    global index
    global path
    path = _path
    index = 0
    user = {}
    with open(path, "r") as f:
        for line in f:
            try:
                key, value = twoarg(line)
            except ValueError:
                if len(line.strip()) == 0 or line.strip()[0] == '#':
                    continue
                raise ParseException("USER attributes require at least a key and a value, separated by a space.\nRead \"%s\"" % line.strip())
            value = replace(value, '\r', '\r\n')
            if re.match('\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}', value):
                value = int(time.mktime(time.strptime(value, "%Y-%m-%d %H:%M:%S")))
            elif value == "VOID":
                value = ""
            user[key] = value
            index += 1
    return user

def parse_esg(_path):
    global index
    global path
    global file
    path = _path
    index = 0
    file = []
    with open(path, "r") as f:
        temp = f.readlines()
    for line in temp:
        if len(line.strip()) > 0 and line.strip()[0] != "#":
            file.append(line)
    return parse_dict(root=True)

def parse_dict(root=False):
    global index
    d = {}
    while True:
        try:
            line = file[index]
        except IndexError:
            if root:
                break
            else:
                raise ParseException("File ended before parse completed. Are you missing and END somewhere?")
        index += 1
        try:
            key, value = twoarg(line)
        except ValueError:
            key = line.strip()
            if key == "SUBJECTS":
                d["subjects"] = parse_subjects()
            elif key == "FIELDS":
                d["fields"] = parse_fields()
            elif key == "END":
                break
            else:
                raise ParseException("DICT children must either be a DICT, LIST, SUBJECTS, or a key/value pair.\nRead \"%s\"" % key)
            continue
        value = replace(value, '\r', '\r\n')
        if key == "DICT":
            d[value] = parse_dict()
        elif key == "LIST":
            d[value] = parse_list()
        else:
            if re.match('\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}', value):
                value = int(time.mktime(time.strptime(value, "%Y-%m-%d %H:%M:%S")))
            elif value == "VOID":
                value = ""
            d[key] = value
    return d

def parse_list():
    global index
    l = []
    while True:
        line = file[index]
        index += 1
        value = replace(line.strip(), '\r', '\r\n')
        if value == "END":
            break
        elif value == "VOID":
            value = ""
        l.append(value)
    return l

def parse_subjects():
    global index
    subjects = {}
    while True:
        line = file[index]
        index += 1
        try:
            key, value = twoarg(line)
        except ValueError:
            end = line.strip()
            if end == "END":
                break
            raise ParseException("SUBJECTS subgroup must be an id and a title, separated by a space.\nRead \"%s\"" % end)
        value = replace(value, '\r', '\r\n')
        subjects[value] = parse_subject(key)
    return subjects

def parse_subject(id):
    global index
    subject = {"id": id}
    while True:
        line = file[index]
        index += 1
        key = line.strip()
        if key == "OPTIONS":
            subject["fields"] = parse_field_radio_options()
        elif key == "END":
            break
        else:
            raise ParseException("subject can only take OPTIONS.\nRead \"%s\"" % key)
    return subject

def parse_fields():
    global index
    fields = []
    while True:
        line = file[index]
        index += 1
        try:
            field, id = twoarg(line)
        except ValueError:
            end = line.strip()
            if end == "END":
                break
            raise ParseException("FIELDS subgroups require a field type (TEXT, TEXTAREA, IMAGE, RADIO) and an id separated by a space.\nRead \"%s\"" % end)
        if field == "TEXT":
            fields.append(parse_field_text(id))
        elif field == "TEXTAREA":
            fields.append(parse_field_textarea(id))
        elif field == "IMAGE":
            fields.append(parse_field_image(id))
        elif field == "RADIO":
            fields.append(parse_field_radio(id))
        else:
            raise ParseException("FIELDS type %s not acceptable" % field)
    return fields

def parse_field_text(id):
    global index
    field = ["text", id, "", ""]
    while True:
        line = file[index]
        index += 1
        try:
            key, value = twoarg(line)
        except ValueError:
            end = line.strip()
            if end == "END":
                break
            raise ParseException("TEXT fields can only have PROMPT and HELPTEXT parameters.\nRead \"%s\"" % end)
        value = replace(value, '\r', '\r\n')
        if value == "VOID":
            value = ""
        if key == "PROMPT":
            field[2] = value
        elif key == "HELPTEXT":
            field[3] = value
        else:
            raise ParseException("FIELD parameter %s not acceptable" % key)
    return field

def parse_field_textarea(id):
    global index
    field = ["textarea", id, "", "", ""]
    while True:
        line = file[index]
        index += 1
        try:
            key, value = twoarg(line)
        except ValueError:
            end = line.strip()
            if end == "END":
                break
            raise ParseException("TEXTAREA fields can only have PROMPT, ROWS, and HELPTEXT parameters.\nRead \"%s\"" % end)
        value = replace(value, '\r', '\r\n')
        if value == "VOID":
            value = ""
        if key == "PROMPT":
            field[2] = value
        elif key == "ROWS":
            field[4] = value
        elif key == "HELPTEXT":
            field[3] = value
        else:
            raise ParseException("FIELD parameter %s not acceptable" % key)
    return field

def parse_field_image(id):
    global index
    field = ["image", id, "", ""]
    while True:
        line = file[index]
        index += 1
        try:
            key, value = twoarg(line)
        except ValueError as exc:
            end = line.strip()
            if end == "END":
                break
            raise ParseException("IMAGE fields can only have PROMPT and HELPTEXT parameters.\nRead \"%s\"" % end)
        value = replace(value, '\r', '\r\n')
        if value == "VOID":
            value = ""
        if key == "PROMPT":
            field[2] = value
        elif key == "HELPTEXT":
            field[3] = value
        else:
            raise ParseException("FIELD parameter %s not acceptable" % key)
    return field

def parse_field_radio(id):
    global index
    field = ["radio", id, "", "", ""]
    while True:
        line = file[index]
        index += 1
        try:
            key, value = twoarg(line)
        except ValueError:
            end = line.strip()
            if end == "END":
                break
            elif end == "OPTIONS":
                field[4] = parse_field_radio_options()
            else:
                raise ParseException("RADIO fields can only have PROMPT, OPTIONS, and HELPTEXT parameters.\nRead \"%s\"" % end)
            continue
        value = replace(value, '\r', '\r\n')
        if value == "VOID":
            value = ""
        if key == "PROMPT":
            field[2] = value
        elif key == "HELPTEXT":
            field[3] = value
        else:
            raise ParseException("FIELD parameter %s not acceptable" % key)
    return field

def parse_field_radio_options():
    global index
    options = []
    while True:
        line = file[index]
        index += 1
        try:
            key, value = twoarg(line)
        except ValueError:
            end = line.strip()
            if end == "END":
                break
            raise ParseException("OPTIONS must have an id and a description, separated by a space.\nRead \"%s\"" % end)
        value = replace(value, '\r', '\r\n')
        options.append([key, value])
    return options

def encode_user(d):
    out = ""
    for key in d:
        value = d[key]
        if isinstance(value, int):
            value = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(value))
        elif value == "":
            value = "VOID"
        value = re.sub("(\r)?\n", "\r", value)
        out += "%s %s\n" % (key, value)
    return out

parser = argparse.ArgumentParser(description="A parser for the esg & user types. Use VOID for blank fields and begin comment lines with #.")
parser.add_argument('--user', type=str, help="path to `.user` file to be parsed")
parser.add_argument('--esg', type=str, help="path to `esg` file to be parsed")
parser.add_argument('--encode', help="read json from stdin into encoded user", action="store_true")
args = parser.parse_args()

if args.user:
    print(json.dumps(parse_user(args.user)))
elif args.esg:
    print(json.dumps(parse_esg(args.esg)))
elif args.encode:
    print(encode_user(json.load(sys.stdin)))
else:
    parser.print_help()