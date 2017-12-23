#!/usr/bin/env python
# -*- coding:utf-8 -*-

from pinyin import PinYin
import sys

test = PinYin('application/libraries/pinyin.py/word.data')
test.load_word()

print test.hanzi2pinyin_split(string=sys.argv[1], split='_')
