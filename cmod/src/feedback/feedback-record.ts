import FeedbackCode from "./feedback-code";

export default class FeedbackRecord {
    protected _codes: Set<FeedbackCode>;
    protected _highest: FeedbackCode | null;

    public constructor() {
        this._codes = new Set<FeedbackCode>();
        this._highest = null;
    }

    public add(code: FeedbackCode): void {
        this._codes.add(code);
        if (this._highest === null || this._highest < code) {
            this._highest = code;
        }
    }

    public delete(code: FeedbackCode): void {
        this._codes.delete(code);
        let arr = Array.from(this.codes);
        this._highest = arr.sort()[this.codes.size - 1];
    }

    public isEmpty(): boolean {
        return this._codes.size <= 0;
    }

    public get highest() {
        return this._highest;
    }

    public get codes() {
        return this._codes;
    }
}
