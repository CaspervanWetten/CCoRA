import Feedback from "src/feedback/feedback";
import { FeedbackResponse } from "src/types";

export default class ResponseToFeedback {
    public static convert(fr: FeedbackResponse) {
        let f = new Feedback();
        for(let i = 0; i < fr.general.length; i++) {
            f.add(fr.general[i]);
        }
        let keys = Object.keys(fr.specific);
        for(let i = 0; i < keys.length; i++) {
            let id = keys[i];
            let items = fr.specific[id];
            for(let j = 0; j < items.length; j++) {
                f.add(items[j], Number(id));
            }
        }
        return f;
    }
}
