(function () {
    let $, SmsCounter;

    window.SmsCounter = SmsCounter = (function () {
        function SmsCounter() {
        }

        SmsCounter.gsm7bitChars = "@Â£$Â¥Ã¨Ã©Ã¹Ã¬Ã²Ã‡\\nÃ˜Ã¸\\rÃ…Ã¥Î”_Î¦Î“Î›Î©Î Î¨Î£Î˜ÎžÃ†Ã¦ÃŸÃ‰ !\\\"#Â¤%&'()*+,-./0123456789:;<=>?Â¡ABCDEFGHIJKLMNOPQRSTUVWXYZÃ„Ã–Ã‘ÃœÂ§Â¿abcdefghijklmnopqrstuvwxyzÃ¤Ã¶Ã±Ã¼Ã ";

        SmsCounter.gsm7bitExChar = "\\^{}\\\\\\[~\\]|â‚¬";

        SmsCounter.gsm7bitRegExp = RegExp("^[" + SmsCounter.gsm7bitChars + "]*$");

        SmsCounter.gsm7bitExRegExp = RegExp("^[" + SmsCounter.gsm7bitChars + SmsCounter.gsm7bitExChar + "]*$");

        SmsCounter.gsm7bitExOnlyRegExp = RegExp("^[\\" + SmsCounter.gsm7bitExChar + "]*$");

        SmsCounter.GSM_7BIT = 'GSM_7BIT';

        SmsCounter.GSM_7BIT_EX = 'GSM_7BIT_EX';

        SmsCounter.UTF16 = 'UTF16';

        SmsCounter.WhatsApp = 'WHATSAPP';

        SmsCounter.messageLength = {
            GSM_7BIT: 160,
            GSM_7BIT_EX: 160,
            UTF16: 70,
            WHATSAPP: 1000
        };

        SmsCounter.multiMessageLength = {
            GSM_7BIT: 153,
            GSM_7BIT_EX: 153,
            UTF16: 67,
            WHATSAPP: 1000
        };

        SmsCounter.count = function (text, type = 'GSM_7BIT') {
            let encoding, length, messages, per_message, remaining;

            if (type === 'WHATSAPP') {
                encoding = 'WHATSAPP';
            } else {
                encoding = this.detectEncoding(text);
            }

            length = text.length;
            if (encoding === this.GSM_7BIT_EX) {
                length += this.countGsm7bitEx(text);
            }


            for (let charPos = 0; charPos < text.length; charPos++) {
                switch (text[charPos]) {
                    case "[":
                    case "]":
                    case "\\":
                    case "^":
                    case "{":
                    case "}":
                    case "|":
                    case "\n":
                        length += 1;
                        break;
                }
            }

            per_message = this.messageLength[encoding];
            if (length > per_message) {
                per_message = this.multiMessageLength[encoding];
            }
            messages = Math.ceil(length / per_message);
            remaining = (per_message * messages) - length;
            if (remaining === 0 && messages === 0) {
                remaining = per_message;
            }
            return {
                encoding: encoding,
                length: length,
                per_message: per_message,
                remaining: remaining,
                messages: messages
            };
        };

        SmsCounter.detectEncoding = function (text) {
            switch (false) {
                case text.match(this.gsm7bitRegExp) == null:
                    return this.GSM_7BIT;
                case text.match(this.gsm7bitExRegExp) == null:
                    return this.GSM_7BIT_EX;
                default:
                    return this.UTF16;
            }
        };

        SmsCounter.countGsm7bitEx = function (text) {
            let char2, chars;
            chars = (function () {
                let _i, _len, _results;
                _results = [];
                for (_i = 0, _len = text.length; _i < _len; _i++) {
                    char2 = text[_i];
                    if (char2.match(this.gsm7bitExOnlyRegExp) != null) {
                        _results.push(char2);
                    }
                }
                return _results;
            }).call(this);
            return chars.length;
        };

        return SmsCounter;

    })();

    if (typeof jQuery !== "undefined" && jQuery !== null) {
        $ = jQuery;
        $.fn.countSms = function (target) {
            let count_sms, input;
            input = this;
            target = $(target);
            count_sms = function () {
                let count, k, v, _results;
                count = SmsCounter.count(input.val());
                _results = [];
                for (k in count) {
                    v = count[k];
                    _results.push(target.find("." + k).text(v));
                }
                return _results;
            };
            this.on('keyup', count_sms);
            return count_sms();
        };
    }

}).call(this);