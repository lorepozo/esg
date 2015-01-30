#!/usr/bin/python

import os
import re
import sys
import shutil

path = os.path.dirname(os.path.realpath(__file__))
version = None

with open(path+"/globals.esg", "r") as f:
    for line in f:
        match = re.match(r"version (\d+)", line)
        if match is None: continue
        version = int(match.group(1))
        break

if version is None:
    sys.stderr.write("Error: globals.esg doesn't contain version.\n")
    exit()

updated = []

for file in [file for file in os.listdir(path) if file.endswith(".esg")]:
    fp = path + "/" + file
    shutil.copy(fp, "%s/esg_records/%d.%s" % (path, version, file))
    with open(fp, "r") as f:
        lines = f.readlines()
    with open(fp, "w") as f:
        for line in lines:
            if re.match(r"^version", line):
                f.write("version %d\n" % (version + 1))
            else:
                f.write(line)
    updated.append(file)

if not updated:
    sys.stderr.write("Failed to update any files.\n")
    exit()
print "Successfully updated %s to version %d" % (", ".join(updated), version + 1)
