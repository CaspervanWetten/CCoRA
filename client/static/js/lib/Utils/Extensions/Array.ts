interface Array<T> {
    swap(i : number, j : number) : void;
    removeAt(index : number) : void;
}

Array.prototype.removeAt = function(index : number)
{
    this.swap(index, this.length - 1);
    this.pop();
}

Array.prototype.swap = function(i : number, j : number)
{
    let temp = this[i];
    this[i] = this[j];
    this[j] = temp;
}