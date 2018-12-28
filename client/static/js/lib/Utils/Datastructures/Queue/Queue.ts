/// <reference path='./QueueNode.ts'/>

class Queue<T>
{
    protected head : QueueNode<T> | undefined;
    protected tail : QueueNode<T> | undefined;

    public constructor()
    {
        this.head = undefined;
        this.tail = undefined;
    }

    public enqueue(x : T)
    {
        let node = new QueueNode(x, undefined);
        if(this.head == undefined) {
            this.head = node;
        } else {
            this.tail!.next = node;
        }
        this.tail = node;
    }
    public dequeue()
    {
        if(this.head != undefined) {
            let r = this.head.body;
            this.head = this.head.next;
            return r;
        } else {
            return undefined;
        }
    }

    public isEmpty()
    {
        return this.head == undefined;
    }
}