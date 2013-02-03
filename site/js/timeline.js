
function QuotationsCanvas(qcanvas, quotes)
{
    this.status = qcanvas.find("div")[0];
    this.canvas = qcanvas.find("canvas")[0];
    this.LINE_WIDTH = 3;
    this.LINE_SKIP = 7;
    this.quotations = quotes;
    this.qlist = qlist = [];
    this.isel = null;
    for (qid in quotes) qlist.push(quotes[qid]);
    if (qlist.length == 0) return;

    qlist.sort(function(q1,q2) {
	return q1.start_time > q2.start_time;
    });
    this.periodStartTime = qlist[0].start_time.getTime();
    periodEnd = qlist.reduce(function(a,b) {return (a.end_time>b.end_time) ? a : b});
    this.periodEndTime = periodEnd.end_time.getTime();
    this.period = this.periodEndTime - this.periodStartTime;

    this.draw();
    $(this.canvas).mousedown(this, this.onMouseDown);
}

// draw
QuotationsCanvas.prototype.draw = function() {
    w = this.canvas.width;
    c = this.canvas.getContext("2d");
    c.fillStyle = "#ffffff";
    c.rect(0,0,this.canvas.width,this.canvas.height);
    c.fill();
    for (i=0; i<qlist.length; i++) {
	lineY = i*this.LINE_SKIP+this.LINE_SKIP/2;
	quote = qlist[i];
	x1 = (quote.start_time.getTime()-this.periodStartTime)*w/this.period;
	x2 = (quote.end_time.getTime()-this.periodStartTime)*w/this.period;
	// minimum line length
	x2 = Math.max(x1+8, x2);
	// line
	if (this.isel == i) c.strokeStyle = "#ffd000"; else c.strokeStyle = "#5070e0";
	c.beginPath();
	c.lineWidth = this.LINE_WIDTH;
	c.moveTo(x1, lineY);
	c.lineTo(x2, lineY);
	c.stroke();
	// ticks
	c.strokeStyle = "#000000";
	c.lineWidth = 2;
	c.beginPath();
	c.moveTo(x1, lineY - this.LINE_WIDTH/2-1);
	c.lineTo(x1, lineY + this.LINE_WIDTH/2+1);
	c.stroke();
	c.beginPath();
	c.moveTo(x2, lineY - this.LINE_WIDTH/2-1);
	c.lineTo(x2, lineY + this.LINE_WIDTH/2+1);
	c.stroke();

    }
}

QuotationsCanvas.prototype.onMouseDown = function(e) {
    qcanvas = e.data;
    offset = $(e.target).offset();
    x = (e.pageX-offset.left);
    y = (e.pageY-offset.top);
    // logical position: 0 = above the center of the first line, 1.5 = between 2. and 3. lines
    pos = (y-qcanvas.LINE_SKIP/2)/qcanvas.LINE_SKIP;
    i=Math.round(pos)
    if (i>=0 && i<qcanvas.qlist.length) {
	if (qcanvas.isel != i) {
	    // get list element with quot id
	    elem = $("#q"+qcanvas.qlist[i].id);
	    qcanvas.status.innerHTML = elem.html();
	    $(qcanvas.status).children().show();
	    qcanvas.isel = i;
	    qcanvas.draw();
	}
    }
}