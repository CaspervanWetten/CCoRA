class QueueNode<T>
{
    public body : T;
    public next : QueueNode<T> | undefined;

    public constructor(body : T, next : QueueNode<T> | undefined)
    {
        this.body = body;
        this.next = next;
    }
}