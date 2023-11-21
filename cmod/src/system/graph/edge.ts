export default class Edge {
    public from: number;
    public to: number;
    public label: string;

    public constructor(from: number, to: number, label: string) {
        this.from = from;
        this.to = to;
        this.label = label;
    }
}
